<?php
/**
 * @var $editor_name
 * @var $text
 * @var $height
 * @var $dir
 */

$skif_path = \Skif\Conf\ConfWrapper::value('skif_path');

$styles = [
    $skif_path .'/assets/libraries/bootstrap/css/bootstrap.min.css',
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

?>
<textarea id="<?php echo $editor_name ?>" name="<?php echo $editor_name ?>" rows="10" class="form-control"><?php echo $text ?></textarea>

<script>
    CKEDITOR.replace('<?php echo $editor_name ?>', {
        toolbar: [
            { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'document', items: [ 'Source' ] },
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] }
        ],
        pasteFilter: 'plain-text',
        customConfig:  '/vendor/websk/skif/assets/js/ckeditor_config.js',
        contentsCss: [<?php echo $contents_css; ?>],
        height: <?php echo $height ?>
    });
</script>
