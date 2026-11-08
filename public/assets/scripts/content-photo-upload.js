(function (global) {
    'use strict';

    const allowedMimeTypes = new Set(['image/gif', 'image/jpeg', 'image/png']);
    const allowedExtensions = new Set(['gif', 'jpeg', 'jpg', 'png']);

    function extensionOf(fileName) {
        const separator = fileName.lastIndexOf('.');
        return separator === -1 ? '' : fileName.slice(separator + 1).toLowerCase();
    }

    function validationError(file, maxFileSize) {
        if (file.size <= 0) {
            return 'Файл пуст.';
        }
        if (file.size > maxFileSize) {
            return 'Размер файла слишком большой.';
        }
        if (!allowedExtensions.has(extensionOf(file.name))
            || (file.type !== '' && !allowedMimeTypes.has(file.type.toLowerCase()))) {
            return 'Этот файл не является поддерживаемым изображением.';
        }
        return null;
    }

    function createFileStatus(output, file) {
        const container = document.createElement('div');
        container.className = 'content-photo-upload-file';

        const name = document.createElement('p');
        name.textContent = file.name;
        container.appendChild(name);

        const progress = document.createElement('div');
        progress.className = 'progress';
        progress.setAttribute('aria-label', 'Прогресс загрузки');

        const progressBar = document.createElement('div');
        progressBar.className = 'progress-bar';
        progressBar.style.width = '0%';
        progressBar.setAttribute('aria-valuemin', '0');
        progressBar.setAttribute('aria-valuemax', '100');
        progressBar.setAttribute('aria-valuenow', '0');
        progress.appendChild(progressBar);
        container.appendChild(progress);

        const message = document.createElement('p');
        container.appendChild(message);
        output.appendChild(container);

        return {
            setProgress(percent) {
                const normalized = Math.max(0, Math.min(100, Math.round(percent)));
                progressBar.style.width = `${normalized}%`;
                progressBar.setAttribute('aria-valuenow', String(normalized));
            },
            setError(text) {
                progressBar.classList.remove('bg-success');
                progressBar.classList.add('bg-danger');
                message.className = 'text-danger';
                message.textContent = text;
            },
            setSuccess() {
                this.setProgress(100);
                progressBar.classList.remove('bg-danger');
                progressBar.classList.add('bg-success');
                message.className = 'text-success';
                message.textContent = 'Файл загружен.';
            },
        };
    }

    function responseJson(xhr) {
        if (xhr.response && typeof xhr.response === 'object') {
            return xhr.response;
        }
        try {
            return JSON.parse(xhr.responseText);
        } catch {
            return null;
        }
    }

    function uploadFile(file, options, status) {
        return new Promise((resolve, reject) => {
            const request = new XMLHttpRequest();
            const formData = new FormData();
            formData.append('upload_image[]', file, file.name);
            formData.append('target_folder', options.targetFolder);

            request.open('POST', options.uploadUrl);
            request.responseType = 'json';
            request.upload.addEventListener('progress', (event) => {
                if (event.lengthComputable) {
                    status.setProgress((event.loaded / event.total) * 100);
                }
            });
            request.addEventListener('load', () => {
                const result = responseJson(request);
                const uploadedFile = result && Array.isArray(result.files) ? result.files[0] : null;
                if (request.status >= 200 && request.status < 300 && uploadedFile && uploadedFile.url) {
                    status.setSuccess();
                    resolve();
                    return;
                }
                reject(new Error(uploadedFile && uploadedFile.error
                    ? uploadedFile.error
                    : 'Файл не удалось загрузить.'));
            });
            request.addEventListener('error', () => reject(new Error('Файл не удалось загрузить.')));
            request.addEventListener('abort', () => reject(new Error('Загрузка файла отменена.')));
            request.send(formData);
        });
    }

    function init(options) {
        const input = document.querySelector(options.inputSelector);
        const output = document.querySelector(options.outputSelector);
        if (!input || !output) {
            throw new Error('Content photo upload controls were not found.');
        }

        const settings = {
            inputSelector: options.inputSelector,
            outputSelector: options.outputSelector,
            uploadUrl: options.uploadUrl,
            targetFolder: options.targetFolder || 'content',
            maxFileSize: options.maxFileSize || 20_000_000,
            onUploaded: typeof options.onUploaded === 'function' ? options.onUploaded : function () {},
        };

        input.addEventListener('change', async () => {
            const files = Array.from(input.files || []);
            input.disabled = true;
            input.parentElement?.classList.add('disabled');
            let uploaded = false;

            for (const file of files) {
                const status = createFileStatus(output, file);
                const error = validationError(file, settings.maxFileSize);
                if (error) {
                    status.setError(error);
                    continue;
                }

                try {
                    await uploadFile(file, settings, status);
                    uploaded = true;
                } catch (uploadError) {
                    status.setError(uploadError instanceof Error
                        ? uploadError.message
                        : 'Файл не удалось загрузить.');
                }
            }

            input.value = '';
            input.disabled = false;
            input.parentElement?.classList.remove('disabled');
            if (uploaded) {
                settings.onUploaded();
            }
        });

        return {validationError};
    }

    global.SkifContentPhotoUpload = {init};
}(window));
