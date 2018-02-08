<?php
/**
 * @var $editor_name
 * @var $text
 * @var $height
 * @var $dir
 */

$skif_path = \Skif\Conf\ConfWrapper::value('skif_path');

$styles = [
    $skif_path . '/assets/libraries/bootstrap/css/bootstrap.min.css',
    '/assets/styles/main.css',
    '/assets/styles/style.css'
];
$config_styles = \Skif\Conf\ConfWrapper::value('ckeditor.styles');
if ($config_styles) {
    $styles = $config_styles;
}

$contents_css = '';
foreach ($config_styles as $style_file) {
    $contents_css .= "'" . $style_file . "'";
}

$dir_str = ($dir ? "'&dir=" . $dir . "'" : '');
?>
<textarea id="<?php echo $editor_name ?>" name="<?php echo $editor_name ?>" rows="10"
          class="form-control"><?php echo $text ?></textarea>

<script>
    CKEDITOR.replace('<?php echo $editor_name ?>', {
        toolbar: [
            { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
            { name: 'tools', items: [ 'Maximize' ] },
            { name: 'document', items: [ 'Source' ] },
            '/',
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
            { name: 'styles', items: ['Format' ] },
            { name: 'about', items: [ 'About' ] }
        ],
        customConfig: '/admin/skif/assets/js/ckeditor_config.js',
        contentsCss: [<?php echo $contents_css; ?>],
        filebrowserBrowseUrl: '/admin/skif/libraries/kcfinder/browse?opener=ckeditor&type=content' + <?php echo($dir ? "'&dir=content/" . $dir . "'" : "''") ?>,
        filebrowserImageBrowseUrl: '/admin/skif/libraries/kcfinder/browse?opener=ckeditor&type=images' + <?php echo($dir ? "'&dir=images/" . $dir . "'" : "''") ?>,
        filebrowserFlashBrowseUrl: '/admin/skif/libraries/kcfinder/browse?opener=ckeditor&type=flash',
        filebrowserUploadUrl: '/admin/skif/libraries/kcfinder/upload?opener=ckeditor&type=content' + <?php echo($dir ? "'&dir=content/" . $dir . "'" : "''") ?>,
        filebrowserImageUploadUrl: '/admin/skif/libraries/kcfinder/upload?opener=ckeditor&type=images' + <?php echo($dir ? "'&dir=images/" . $dir . "'" : "''") ?>,
        filebrowserFlashUploadUrl: '/admin/skif/libraries/kcfinder/upload?opener=ckeditor&type=flash',
        height: <?php echo $height ?>
    });
</script>
