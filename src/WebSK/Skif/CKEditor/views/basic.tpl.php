<?php
/**
 * @var $editor_name
 * @var $text
 * @var $height
 * @var $dir
 */

use WebSK\Config\ConfWrapper;
use WebSK\Skif\SkifPath;

$config_styles = ConfWrapper::value('ckeditor.styles', []);
$contents_css_files = [];
foreach ($config_styles as $style_file) {
    $contents_css_files[] = "'" . $style_file . "'";
}
$contents_css = implode(',', $contents_css_files);

$filemanager_path = ConfWrapper::value('ckeditor.filemanager_path');
if (empty($filemanager_path)) {
    $filemanager_path = SkifPath::wrapSkifAssetsVersion('/libraries/filemanager/index.html');
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
        customConfig: '<?php echo SkifPath::wrapSkifAssetsVersion('/scripts/ckeditor_config.js'); ?>',
        contentsCss: [<?php echo $contents_css; ?>],
        filebrowserBrowseUrl: '<?php echo $filemanager_path; ?>' + <?php echo($dir ? "'?expandedFolder=" . $dir . "'" : "''") ?>,
        filebrowserImageBrowseUrl: '<?php echo $filemanager_path; ?>' + <?php echo($dir ? "'?expandedFolder=images/" . $dir . "'" : "''") ?>,
        height: <?php echo $height ?>
    });
</script>
