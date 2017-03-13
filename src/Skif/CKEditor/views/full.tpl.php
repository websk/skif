<?php
/**
 * @var $editor_name
 * @var $text
 * @var $height
 * @var $dir
 */

$bower_path = \Skif\Conf\ConfWrapper::value('bower_path');

$dir_str = ($dir ? "'&dir=". $dir . "'" : '');
?>
<textarea id="<?php echo $editor_name ?>" name="<?php echo $editor_name ?>" rows="10" class="form-control"><?php echo $text ?></textarea>

<script>
    CKEDITOR.replace('<?php echo $editor_name ?>', {
        customConfig:  '/vendor/websk/skif/assets/js/ckeditor_config.js',
        contentsCss: ['<?php echo $bower_path; ?>/assets/libraries/bootstrap/css/bootstrap.min.css', '/assets/styles/main.css', '/assets/styles/style.css'],
        filebrowserBrowseUrl: '/vendor/websk/skif/libraries/kcfinder/browse.php?opener=ckeditor&type=content' + <?php echo ($dir ? "'&dir=content/" . $dir . "'" : "''") ?>,
        filebrowserImageBrowseUrl: '/vendor/websk/skif/libraries/kcfinder/browse.php?opener=ckeditor&type=images' + <?php echo ($dir ? "'&dir=images/" . $dir . "'" : "''") ?>,
        filebrowserFlashBrowseUrl: '/vendor/websk/skif/libraries/kcfinder/browse.php?opener=ckeditor&type=flash',
        filebrowserUploadUrl: '/vendor/websk/skif/libraries/kcfinder/upload.php?opener=ckeditor&type=content' + <?php echo ($dir ? "'&dir=content/" . $dir . "'" : "''") ?>,
        filebrowserImageUploadUrl: '/vendor/websk/skif/libraries/kcfinder/upload.php?opener=ckeditor&type=images' + <?php echo ($dir ? "'&dir=images/" . $dir . "'" : "''") ?>,
        filebrowserFlashUploadUrl: '/vendor/websk/skif/libraries/kcfinder/upload.php?opener=ckeditor&type=flash',
        height: <?php echo $height ?>
    });
</script>
