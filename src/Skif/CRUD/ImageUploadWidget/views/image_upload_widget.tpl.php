<?php
/**
 * @var $default_folder_name string
 * @var $field_name string
 * @var $field_value string
 */
?>

<script>
    var skif = skif || {};

    skif.admin = {};
    skif.admin.utils = {};
    skif.admin.utils.crud = {};

    skif.admin.utils.crud.imageAjaxUploadWidget = function (formField) {
        var currentParent = $("#imageUploadWidget_" + formField);
        var fileInput = currentParent.find('.file-select');
        var fileInputBtn = currentParent.find('.select-file-btn');

        var uploadFiles = function (event) {
            event.stopPropagation();
            event.preventDefault();

            var files = fileInput.prop('files'),
                uploadButton = currentParent.find('.upload-button'),
                data = new FormData(),
                target_folder_select = currentParent.find('.target_folder'),
                not_force_file_extension = currentParent.find('.not_force_file_extension');

            uploadButton.text("Загружается...").attr('disabled', 'disabled');

            $.each(files, function (key, value) {
                data.append(key, value);
            });

            $.each(target_folder_select, function (key, value) {
                data.append('target_folder', value.value);
            });

            $.each(not_force_file_extension, function (key, value) {
                if ($(value).prop("checked") == true) {
                    data.append('not_force_file_extension', value.value);
                }
            });

            $.ajax({
                url: '/images/upload_to_images',
                type: 'POST',
                data: data,
                cache: false,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function (response) {
                    currentParent.find('.uploaded-image').html('<div class="uploaded-image__preview">' +
                    '    <img src="<?=\Skif\CRUD\ImageUploadWidget\ImageUploadWidget::getIconImagePresetFolder()?>/' + response.fileName + '" />' +
                    '</div>');
                    currentParent.find('.internal-file-name').val(response.fileName);
                },
                error: function (jqXHR, textStatus) {
                    alert("Произошла ошибка, попробуйте загрузить еще раз");
                },
                complete: function () {
                    uploadButton.text("Загрузить").removeAttr('disabled');
                }
            });
        };

        currentParent.on('click', '.upload-button', uploadFiles);

        fileInputBtn.on('click', function(event) {
            event.preventDefault();
            fileInput.click();
        });
    };
</script>

<?php

$images_folder = '/files/images';

$dirty_images_subdirs_arr = glob($images_folder . '/*', GLOB_ONLYDIR);
$images_subdirs_arr = array();

foreach ($dirty_images_subdirs_arr as $subdir) {

    // проверяем, что путь начинается с папки картинок
    if (mb_substr($subdir, 0, mb_strlen($images_folder)) != $images_folder) {
        //continue;
    }

    // вырезаем часть пути внутри папки картинок
    $subdir_in_images = mb_substr($subdir, mb_strlen($images_folder) + mb_strlen(DIRECTORY_SEPARATOR));


    $images_subdirs_arr[] = $subdir_in_images;
}

if (!empty($default_folder_name)) {
    if (!in_array($default_folder_name , $images_subdirs_arr)) {
        mkdir($_SERVER['DOCUMENT_ROOT'].'/'.$images_folder.'/'.$default_folder_name);
    }

    $images_subdirs_arr = array($default_folder_name);
}
?>

<div id="imageUploadWidget_<?php echo $field_name ?>">
    <?php
    if (count($images_subdirs_arr)) {
        echo '<div>Загрузить в папку: <select name="target_folder" class="target_folder">';

        if (empty($default_folder_name)) {
            echo '<option value="" selected>';
        }

        foreach ($images_subdirs_arr as $subdir) {
            echo '<option value="' . $subdir . '">' . $subdir;
        }

        echo "</select></div>";
    }
    ?>

    <div class="file-upload">
        <div class="row">
            <div class="col-sm-8">
                <input type="file" class="file-select form-control" name="photo" />
                <button type="submit" class="btn btn-default select-file-btn">Выберите файл</button>
            </div>
            <div class="col-sm-4">
                <button type="submit" class="btn btn-default pull-right upload-button">Загрузить</button>
            </div>
        </div>
    </div>

    <div class="uploaded-image">
    <?php if ($field_value) { ?>
        <a class="uploaded-image__previewtop" target="_blank" href="<?php echo \Skif\Image\ImageManager::getImgUrlByPreset($field_value, \Skif\Image\ImagePresets::IMAGE_PRESET_UPLOAD); ?>">
            <img src="<?php echo \Skif\CRUD\ImageUploadWidget\ImageUploadWidget::getIconFileUrlByFilename($field_value); ?>">
        </a>
    <?php } ?>
    </div>
    <input type="hidden" name="<?php echo $field_name ?>" class="internal-file-name" value="<?php echo $field_value ?>">
</div>

<script>
    (function () {
        skif.admin.utils.crud.imageAjaxUploadWidget("<?php echo $field_name ?>");
    })();
</script>