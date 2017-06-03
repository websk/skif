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

?>
<textarea id="<?php echo $editor_name ?>" name="<?php echo $editor_name ?>" rows="10" class="form-control"><?php echo $text ?></textarea>

<script>
    CKEDITOR.replace('<?php echo $editor_name ?>', {
        toolbar: [
            { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source'] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { items: [ 'Format'] },
            { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
            '/',
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
            { name: 'others', items: [ '-' ] }
        ],
        customConfig: '/vendor/websk/skif/assets/js/ckeditor_config.js',
        contentsCss: [<?php echo implode(',', $styles); ?>],
        filebrowserBrowseUrl: '/vendor/websk/skif/libraries/kcfinder/browse.php?opener=ckeditor&type=content' + <?php echo ($dir ? "'&dir=content/" . $dir . "'" : "''") ?>,
        filebrowserImageBrowseUrl: '/vendor/websk/skif/libraries/kcfinder/browse.php?opener=ckeditor&type=images' + <?php echo ($dir ? "'&dir=images/" . $dir . "'" : "''") ?>,
        filebrowserFlashBrowseUrl: '/vendor/websk/skif/libraries/kcfinder/browse.php?opener=ckeditor&type=flash',
        filebrowserUploadUrl: '/vendor/websk/skif/libraries/kcfinder/upload.php?opener=ckeditor&type=content' + <?php echo ($dir ? "'&dir=content/" . $dir . "'" : "''") ?>,
        filebrowserImageUploadUrl: '/vendor/websk/skif/libraries/kcfinder/upload.php?opener=ckeditor&type=images' + <?php echo ($dir ? "'&dir=images/" . $dir . "'" : "''") ?>,
        filebrowserFlashUploadUrl: '/vendor/websk/skif/libraries/kcfinder/upload.php?opener=ckeditor&type=flash',
        height: <?php echo $height ?>
    });
</script>
