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
        customConfig: '<?php echo Path::wrapSkifAssetsVersion('/js/ckeditor_config.js'); ?>',
        contentsCss: [<?php echo $contents_css; ?>],
        filebrowserBrowseUrl: '<?php echo Path::wrapSkifUrlPath('/libraries/kcfinder/browse.php?opener=ckeditor&type=content'); ?>' + <?php echo($dir ? "'&dir=content/" . $dir . "'" : "''") ?>,
        filebrowserImageBrowseUrl: '<?php echo Path::wrapSkifUrlPath('/libraries/kcfinder/browse.php?opener=ckeditor&type=images'); ?>' + <?php echo($dir ? "'&dir=images/" . $dir . "'" : "''") ?>,
        filebrowserFlashBrowseUrl: '<?php echo Path::wrapSkifUrlPath('/libraries/kcfinder/browse.php?opener=ckeditor&type=flash'); ?>',
        filebrowserUploadUrl: '<?php echo Path::wrapSkifUrlPath('/libraries/kcfinder/upload.php?opener=ckeditor&type=content'); ?>' + <?php echo($dir ? "'&dir=content/" . $dir . "'" : "''") ?>,
        filebrowserImageUploadUrl: '<?php echo Path::wrapSkifUrlPath('/libraries/kcfinder/upload.php?opener=ckeditor&type=images'); ?>' + <?php echo($dir ? "'&dir=images/" . $dir . "'" : "''") ?>,
        filebrowserFlashUploadUrl: '<?php echo Path::wrapSkifUrlPath('/libraries/kcfinder/upload.php?opener=ckeditor&type=flash'); ?>',
        height: <?php echo $height ?>
    });
</script>
