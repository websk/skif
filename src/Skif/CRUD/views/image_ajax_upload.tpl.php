<?php
/**
 * @var $field_name string
 * @var $field_value string
 */
?>

<script>
    var gmbox = gmbox || {};

    gmbox.admin = {};
    gmbox.admin.utils = {};
    gmbox.admin.utils.crud = {};

    gmbox.admin.utils.crud.imageAjaxUploadWidget = function (formField) {
        var currentParent = $("#imageUploadWidget_" + formField);

        var uploadFiles = function (event) {
            event.stopPropagation();
            event.preventDefault();

            var files = currentParent.find('.file-select').prop('files'),
                uploadButton = currentParent.find('.upload-button'),
                data = new FormData(),
                target_folder_select = currentParent.find('.target_folder');

            uploadButton.text("Загружается...").attr('disabled', 'disabled');

            $.each(files, function (key, value) {
                data.append(key, value);
            });

            $.each(target_folder_select, function (key, value) {
                data.append('target_folder', value.value);
            });

            $.ajax({
                url: '/images/upload',
                type: 'POST',
                data: data,
                cache: false,
                dataType: 'text',
                processData: false,
                contentType: false,
                success: function (response) {
                    currentParent.find('.uploaded-image').html($("<img src='/images/styles/131_91/" + response + "'>"));
                    currentParent.find('.internal-file-name').val(response);
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
    };
</script>


<?php

$images_folder = 'images';

$dirty_images_subdirs_arr = glob($images_folder . '/*', GLOB_ONLYDIR);
$images_subdirs_arr = array();

foreach ($dirty_images_subdirs_arr as $subdir) {

    // проверяем, что путь начинается с папки картинок
    if (mb_substr($subdir, 0, mb_strlen($images_folder)) != $images_folder) {
        continue;
    }

    // вырезаем часть пути внутри папки картинок
    $subdir_in_images = mb_substr($subdir, mb_strlen($images_folder) + mb_strlen(DIRECTORY_SEPARATOR));

    // пропускаем папку styles - в нее закачивать нельзя
    if ($subdir_in_images == 'styles') {
        continue;
    }

    $images_subdirs_arr[] = $subdir_in_images;
}

?>

<div id="imageUploadWidget_<?php echo $field_name ?>">

    <?php

    if (count($images_subdirs_arr)) {
        echo '<div>Загрузить в папку: <select name="target_folder" class="target_folder">';
        echo '<option value="" selected>';
        foreach ($images_subdirs_arr as $subdir) {
            echo '<option value="' . $subdir . '">' . $subdir;
        }
        echo "</select></div>";
    }

    ?>

    <div class="file-upload">
        <input type="file" class="file-select" name="photo">
        <button type="submit" class="upload-button">Загрузить</button>
    </div>

    <div class="uploaded-image">
        <?php
        if ($field_value) {
            ?>
            <img src="<?php echo \WebSK\Skif\Image\ImageManager::getImgUrlByPreset($field_value, '131_91') ?>">
        <?php } ?>
    </div>
    <input type="hidden" name="<?php echo $field_name ?>" class="internal-file-name" value="<?php echo $field_value ?>">
</div>

<script>
    (function () {
        gmbox.admin.utils.crud.imageAjaxUploadWidget("<?php echo $field_name ?>");
    })();
</script>