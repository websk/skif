<?php
/**
 * @var string $editor_name
 * @var string $text
 * @var int $height
 * @var string $dir
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
    $filemanager_path = SkifPath::wrapAssetsVersion('/libraries/filemanager/index.html');
}

$text = str_replace("&nbsp;","",$text);
?>
<textarea id="<?php echo $editor_name ?>" name="<?php echo $editor_name ?>" rows="10"
          class="form-control"><?php echo $text ?></textarea>

<script>
    editor = CKEDITOR.replace('<?php echo $editor_name ?>', {
        toolbar: [
            {name: 'clipboard', items: ['Cut', 'Copy', 'Paste', '-', 'RemoveFormat', '-', 'Undo', 'Redo', 'Find']},
            {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript']},
            {name: 'styles', items: ['Format']},
            {name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']},
            {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
            {name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar']},
            {name: 'tools', items: ['Maximize', 'Source']},
        ],
        customConfig: '<?php echo SkifPath::wrapAssetsVersion('/scripts/ckeditor_config.js'); ?>',
        contentsCss: [<?php echo $contents_css; ?>],
        filebrowserBrowseUrl: '<?php echo $filemanager_path; ?>' + <?php echo($dir ? "'?expandedFolder=" . $dir . "'" : "''") ?>,
        filebrowserImageBrowseUrl: '<?php echo $filemanager_path; ?>' + <?php echo($dir ? "'?expandedFolder=images/" . $dir . "'" : "''") ?>,
        height: <?php echo $height ?>
    });

    editor.on('paste', function (evt) {
        evt.data.dataValue = evt.data.dataValue.replace(/&nbsp;/g, '');
    }, null, null, 9);
</script>


