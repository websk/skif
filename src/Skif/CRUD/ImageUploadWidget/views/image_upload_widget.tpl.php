<?php
/**
 * @var $default_folder_name string
 * @var $field_name string
 * @var $field_value string
 */

$bower_path = \Skif\Conf\ConfWrapper::value('bower_path');
?>

<span class="btn btn-success fileinput-button">
        <i class="glyphicon glyphicon-plus"></i>
        <span>Выберите файл...</span>
        <input id="<?php echo $field_name; ?>" type="file" name="<?php echo $field_name; ?>">
    </span>
<br>
<br>

<div id="files" class="files"></div>

<link rel="stylesheet" href="<?php echo $bower_path; ?>/blueimp-file-upload/css/jquery.fileupload.css">
<script src="<?php echo $bower_path; ?>/blueimp-file-upload/js/jquery.fileupload.js"></script>
<script src="<?php echo $bower_path; ?>/blueimp-file-upload/js/jquery.fileupload-process.js"></script>
<script src="<?php echo $bower_path; ?>/blueimp-file-upload/js/jquery.fileupload-validate.js"></script>

<script>
    function ajaxUpdateImageList() {
        $.ajax({
            url: '/tour/list_photo/<?php echo $tour_id; ?>',
            success: function (html) {
                $('#tour_photos_list').html(html);
            }
        });
    }

    function ajaxDeleteImage(tour_photo_id) {
        $.ajax({
            type: "POST",
            url: "/tour/delete_photo/" + tour_photo_id,
            success: function () {
                ajaxUpdateImageList();
            }
        });
    }

    $(function () {
        ajaxUpdateImageList();

        var url = '/tour/add_photo/<?php echo $tour_id; ?>';

        $('#<?php echo $field_name; ?>').fileupload({
            url: url,
            dataType: 'json',
            autoUpload: true,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            maxFileSize: 5242880,
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
