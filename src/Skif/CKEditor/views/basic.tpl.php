<?php
/**
 * @var $editor_name
 * @var $text
 * @var $height
 * @var $dir
 */

use Skif\Conf\ConfWrapper;

$skif_path = ConfWrapper::value('skif_path');

$styles = [
    $skif_path . '/assets/libraries/bootstrap/css/bootstrap.min.css',
    '/assets/styles/main.css',
    '/assets/styles/style.css'
];
$config_styles = ConfWrapper::value('ckeditor.styles', []);
if ($config_styles) {
    $styles = $config_styles;
}

$contents_css = '';
foreach ($config_styles as $style_file) {
    $contents_css .= "'" . $style_file . "'";
}

?>
<textarea id="<?php echo $editor_name ?>" name="<?php echo $editor_name ?>" rows="10"
          class="form-control"><?php echo $text ?></textarea>

<script>
    CKEDITOR.replace('<?php echo $editor_name ?>', {
        toolbar: [
            { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
            { name: 'document', items: [ 'Source' ] },
            '/',
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] }
        ],
        customConfig: '<?php echo $skif_path; ?>/assets/js/ckeditor_config.js',
        contentsCss: [<?php echo $contents_css; ?>],
        filebrowserBrowseUrl: '<?php echo $skif_path; ?>/libraries/kcfinder/browse.php?opener=ckeditor&type=content' + <?php echo($dir ? "'&dir=content/" . $dir . "'" : "''") ?>,
        filebrowserImageBrowseUrl: '<?php echo $skif_path; ?>/libraries/kcfinder/browse.php?opener=ckeditor&type=images' + <?php echo($dir ? "'&dir=images/" . $dir . "'" : "''") ?>,
        filebrowserFlashBrowseUrl: '<?php echo $skif_path; ?>/libraries/kcfinder/browse.php?opener=ckeditor&type=flash',
        filebrowserUploadUrl: '<?php echo $skif_path; ?>/libraries/kcfinder/upload.php?opener=ckeditor&type=content' + <?php echo($dir ? "'&dir=content/" . $dir . "'" : "''") ?>,
        filebrowserImageUploadUrl: '<?php echo $skif_path; ?>/libraries/kcfinder/upload.php?opener=ckeditor&type=images' + <?php echo($dir ? "'&dir=images/" . $dir . "'" : "''") ?>,
        filebrowserFlashUploadUrl: '<?php echo $skif_path; ?>/libraries/kcfinder/upload.php?opener=ckeditor&type=flash',
        height: <?php echo $height ?>
    });
</script>
