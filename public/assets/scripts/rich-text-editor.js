(function (window) {
    'use strict';

    let activeEditor = null;
    let fileManagerWindow = null;
    let resourceBaseUrl = null;

    function openFileManager(editor, baseUrl, directory) {
        const url = new URL(baseUrl, window.location.href);
        if (directory) {
            url.searchParams.set('expandedFolder', directory);
        }

        activeEditor = editor;
        fileManagerWindow = window.open(
            url.toString(),
            'skif-rich-filemanager',
            'width=1100,height=700,resizable=yes,scrollbars=yes',
        );
    }

    function isImage(resourceObject, url) {
        const attributes = resourceObject && resourceObject.attributes ? resourceObject.attributes : {};
        const mimeType = attributes.mime || attributes.mimetype || attributes.type || '';
        const name = attributes.name || url;

        return mimeType.startsWith('image/') || /\.(?:avif|gif|jpe?g|png|svg|webp)$/i.test(name);
    }

    function insertResource(editor, resourceObject, url) {
        const attributes = resourceObject && resourceObject.attributes ? resourceObject.attributes : {};
        const name = attributes.name || url.split('/').pop() || url;

        if (isImage(resourceObject, url)) {
            const image = editor.createInside.element('img');
            image.setAttribute('src', url);
            image.setAttribute('alt', name);
            editor.s.insertNode(image);
        } else {
            const link = editor.createInside.element('a');
            link.setAttribute('href', url);
            link.textContent = name;
            editor.s.insertNode(link);
        }

        editor.synchronizeValues();
        editor.focus();
    }

    window.addEventListener('message', function (event) {
        if (
            !activeEditor ||
            !fileManagerWindow ||
            event.source !== fileManagerWindow ||
            event.origin !== window.location.origin ||
            !event.data ||
            event.data.source !== 'richfilemanager' ||
            typeof event.data.preview_url !== 'string'
        ) {
            return;
        }

        const resourceUrl = new URL(event.data.preview_url, resourceBaseUrl);
        if (!['http:', 'https:'].includes(resourceUrl.protocol)) {
            return;
        }
        insertResource(activeEditor, event.data.resourceObject, resourceUrl.href);
        activeEditor = null;
        fileManagerWindow = null;
    });

    window.SkifRichTextEditor = {
        init: function (options) {
            const imageDirectory = options.directory ? 'images/' + options.directory : 'images';
            const baseUrl = new URL(options.baseUrl || '/', window.location.origin).href;
            const textarea = document.getElementById(options.elementId);
            const content = new window.DOMParser().parseFromString(textarea.value, 'text/html');
            content.querySelectorAll('img[src]').forEach(function (image) {
                const source = image.getAttribute('src');
                if (source && !/^(?:[a-z][a-z\d+.-]*:|\/|#)/i.test(source)) {
                    image.setAttribute('src', new URL(source, baseUrl).href);
                }
            });
            textarea.value = content.body.innerHTML;

            return Jodit.make(textarea, {
                language: 'ru',
                height: options.height,
                enter: 'br',
                iframe: true,
                iframeBaseUrl: baseUrl,
                iframeCSSLinks: options.styles,
                iframeStyle: Jodit.defaultOptions.iframeStyle +
                    'body{font-family:var(--bs-body-font-family);font-size:var(--bs-body-font-size);' +
                    'font-weight:var(--bs-body-font-weight);line-height:var(--bs-body-line-height);' +
                    'color:var(--bs-body-color);background:var(--bs-body-bg);}',
                events: {
                    afterInit: function (editor) {
                        editor.editor.id = 'content';
                        editor.editor.classList.add('content-body');
                    },
                },
                beautifyHTML: false,
                toolbarAdaptive: false,
                sourceEditorCDNUrlsJS: [options.sourceEditorUrl],
                sourceEditorNativeOptions: {
                    theme: 'ace/theme/crimson_editor',
                },
                cleanHTML: {
                    replaceNBSP: true,
                    removeEventAttributes: true,
                    safeJavaScriptLink: true,
                    safeLinksTarget: true,
                },
                buttons: [
                    'source', '|',
                    'undo', 'redo', '|',
                    'bold', 'italic', 'underline', 'strikethrough', 'eraser', '|',
                    'paragraph', 'ul', 'ol', 'outdent', 'indent', 'blockquote', '|',
                    'link', 'table', 'hr', 'symbols', '|',
                    {
                        name: 'skifImageManager',
                        icon: 'image',
                        tooltip: 'Выбрать изображение',
                        exec: function (editor) {
                            resourceBaseUrl = baseUrl;
                            openFileManager(editor, options.fileManagerUrl, imageDirectory);
                        },
                    },
                    {
                        name: 'skifFileManager',
                        icon: 'file',
                        tooltip: 'Выбрать файл',
                        exec: function (editor) {
                            resourceBaseUrl = baseUrl;
                            openFileManager(editor, options.fileManagerUrl, options.directory);
                        },
                    }, '|',
                    'find', 'fullsize',
                ],
            });
        },
    };
})(window);
