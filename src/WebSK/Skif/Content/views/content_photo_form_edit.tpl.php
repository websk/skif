<?php
/**
 * @var $content_type
 * @var $content_id
 */

use WebSK\Skif\Content\ContentRoutes;
use WebSK\Skif\SkifPath;
use WebSK\Slim\Router;

if ($content_id == 'new') {
    echo 'Чтобы появилась возможность добавлять фотографии, создайте сначала материал';
    return;
}


?>
<div id="content_photos_list"></div>

<h4>Добавить фото</h4>

<span class="btn btn-success fileinput-button">
    <i class="glyphicon glyphicon-plus"></i>
    <span>Выберите файлы...</span>
    <input id="upload_image" type="file" name="upload_image[]" multiple>
</span>
<br>
<br>

<div id="files" class="files"></div>

<link rel="stylesheet"
      href="<?php echo SkifPath::wrapAssetsVersion('/libraries/blueimp-file-upload/css/jquery.fileupload.css') ?>">
<script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/blueimp-file-upload/js/jquery.fileupload.js') ?>"></script>
<script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/blueimp-file-upload/js/jquery.fileupload-process.js') ?>"></script>
<script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/blueimp-file-upload/js/jquery.fileupload-validate.js') ?>"></script>

<script>
    $(document).ready(function () {
        $("a.grouped_elements").fancybox({
            openEffect: 'none',
            closeEffect: 'none'
        });
    });

    function ajaxDeleteImage(content_photo_id) {
        $.ajax({
            type: "POST",
            url: "/admin/content_photo/" + content_photo_id + "/delete",
            success: function () {
                ajaxUpdateImageList();
            }
        });
    }

    function ajaxSetDefaultImage(content_photo_id) {
        $.ajax({
            type: "POST",
            url: "/admin/content_photo/" + content_photo_id + "/set_default",
            success: function () {
                ajaxUpdateImageList();
            }
        });
    }

    function ajaxUpdateImageList() {
        $.ajax({
            url: '<?php echo Router::pathFor(ContentRoutes::ROUTE_NAME_ADMIN_CONTENT_PHOTO_LIST, ['content_type' => $content_type, 'content_id' => $content_id]); ?>',
            success: function (html) {
                $('#content_photos_list').html(html);
            }
        });
    }

    $(function () {
        ajaxUpdateImageList();

        var url = '<?php echo Router::pathFor(ContentRoutes::ROUTE_NAME_ADMIN_CONTENT_PHOTO_CREATE, ['content_type' => $content_type, 'content_id' => $content_id]); ?>';

        $('#upload_image').fileupload({
            url: url,
            dataType: 'json',
            formData: [{name: 'target_folder', value: 'content'}],
            autoUpload: true,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            maxFileSize: 20971520,
            previewThumbnail: false,
            messages: {
                maxNumberOfFiles: 'Превышено максимальное количество файлов, загружаемое за один раз',
                acceptFileTypes: 'Этот файл не изображение',
                maxFileSize: 'Размер файла слишком большой',
                minFileSize: 'Размер файла слишком маленький'
            }
        }).on('fileuploadadd', function (e, data) {
            data.context = $('<div/>').appendTo('#files');
            $.each(data.files, function (index, file) {
                var node = $('<p/>')
                    .append($('<span/>').text(file.name));
                node.appendTo(data.context);
            });
        }).on('fileuploadprocessalways', function (e, data) {
            var index = data.index,
                file = data.files[index],
                node = $(data.context.children()[index]);
            if (file.error) {
                node
                    .append('<br>')
                    .append($('<span class="text-danger"/>').text(file.error));
            }
        }).on('fileuploaddone', function (e, data) {
            $.each(data.result.files, function (index, file) {
                if (file.url) {
                    $(data.context.children()[index])
                        .append(' <div class="text-success">Файл  загружен</div>');
                } else if (file.error) {
                    var error = $('<span class="text-danger"/>').text(file.error);
                    $(data.context.children()[index])
                        .append('<br>')
                        .append(error);
                }
            });
            ajaxUpdateImageList();
        }).on('fileuploadfail', function (e, data) {
            $.each(data.files, function (index) {
                var error = $('<span class="text-danger"/>').text('Файл не удалось загрузить.');
                $(data.context.children()[index])
                    .append('<br>')
                    .append(error);
            });
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');
    });
</script>
