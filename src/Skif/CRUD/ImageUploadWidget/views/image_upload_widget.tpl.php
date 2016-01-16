<?php
/**
 * @var $target_folder string
 * @var $field_name string
 * @var $field_value string
 */

$bower_path = \Skif\Conf\ConfWrapper::value('bower_path');

if ($field_value) {
    $image_path = $target_folder . DIRECTORY_SEPARATOR . $field_value;
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $("a.grouped_elements").fancybox({
                openEffect: 'none',
                closeEffect: 'none'
            });
        });
    </script>
    <a rel="gallery" href="<?php echo \Skif\Image\ImageManager::getImgUrlByFileName($image_path) ?>"
       class="grouped_elements">
        <img src="<?php echo \Skif\Image\ImageManager::getImgUrlByPreset($image_path, '160_auto') ?>"
             class="img-responsive img-thumbnail" border="0">
    </a>
    <?php
}
?>
<input type="hidden" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" value="">

<span class="btn btn-success fileinput-button">
    <i class="glyphicon glyphicon-plus"></i>
    <span>Выберите файл...</span>
    <input id="upload_image" type="file" name="upload_image">
    <input type="hidden" name="target_folder" value="<?php echo $target_folder; ?>">
</span>

<div id="files" class="files"></div>

<script src="<?php echo $bower_path; ?>/blueimp-load-image/js/load-image.all.min.js"></script>
<link rel="stylesheet" href="<?php echo $bower_path; ?>/blueimp-file-upload/css/jquery.fileupload.css">
<script src="<?php echo $bower_path; ?>/blueimp-file-upload/js/jquery.fileupload.js"></script>
<script src="<?php echo $bower_path; ?>/blueimp-file-upload/js/jquery.fileupload-process.js"></script>
<script src="<?php echo $bower_path; ?>/blueimp-file-upload/js/jquery.fileupload-image.js"></script>
<script src="<?php echo $bower_path; ?>/blueimp-file-upload/js/jquery.fileupload-validate.js"></script>

<script>
    $(function () {
        var url = '/images/upload';

        $('#upload_image').fileupload({
            url: url,
            dataType: 'json',
            autoUpload: true,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            maxFileSize: 5242880,
            previewThumbnail: true,
            previewMaxWidth: 100,
            previewMaxHeight: 100,
            previewCrop: true,
            maxNumberOfFiles: 1,
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
            if (file.preview) {
                node
                    .prepend('<br>')
                    .prepend(file.preview);
            }
            if (file.error) {
                node
                    .append('<br>')
                    .append($('<span class="text-danger"/>').text(file.error));
            }
            if (index + 1 === data.files.length) {
                data.context.find('button')
                    .text('Upload')
                    .prop('disabled', !!data.files.error);
            }
        }).on('fileuploaddone', function (e, data) {
            $.each(data.result.files, function (index, file) {
                if (file.url) {
                    $(data.context.children()[index])
                        .append(' <div class="text-success">Файл  загружен</div>');
                    $('.fileinput-button').remove();
                    $('.grouped_elements').remove();
                    $('#<?php echo $field_name; ?>').val(file.name);
                } else if (file.error) {
                    var error = $('<span class="text-danger"/>').text(file.error);
                    $(data.context.children()[index])
                        .append('<br>')
                        .append(error);
                }
            });
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
