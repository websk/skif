<?php
/**
 * @var $editor_name
 * @var $text
 * @var $height
 * @var $dir
 */

use Skif\Conf\ConfWrapper;
use Websk\Skif\Path;

$config_styles = ConfWrapper::value('ckeditor.styles', []);
$contents_css_files = [];
foreach ($config_styles as $style_file) {
    $contents_css_files[] = "'" . $style_file . "'";
}
$contents_css = implode(',', $contents_css_files);
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
        customConfig:  '<?php echo Path::wrapSkifAssetsVersion('/js/ckeditor_config.js'); ?>',
        contentsCss: [<?php echo $contents_css; ?>],
        height: <?php echo $height ?>
    });
</script>
