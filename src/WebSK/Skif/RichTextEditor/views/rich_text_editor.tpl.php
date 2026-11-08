<?php
/**
 * @var string $editor_name
 * @var string $text
 * @var int $height
 * @var string $directory
 */

use WebSK\Config\ConfWrapper;
use WebSK\Skif\SkifPath;

$styles = ConfWrapper::value(
    'rich_text_editor.styles',
    ConfWrapper::value('ckeditor.styles', [])
);
if (empty($styles)) {
    $styles = array_map(SkifPath::wrapAssetsVersion(...), [
        '/libraries/bootstrap/css/bootstrap.min.css',
        '/libraries/admin-lte/css/AdminLTE.min.css',
        '/libraries/crud/crud.css',
        '/styles/main.css',
    ]);
}

$filemanager_path = ConfWrapper::value(
    'rich_text_editor.filemanager_path',
    ConfWrapper::value('ckeditor.filemanager_path')
);
if (empty($filemanager_path)) {
    $filemanager_path = SkifPath::wrapAssetsVersion('/libraries/filemanager/index.html');
}

$text = str_replace('&nbsp;', '', $text);
?>
<textarea id="<?php echo htmlspecialchars($editor_name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"
          name="<?php echo htmlspecialchars($editor_name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"
          data-skif-rich-text-editor
          rows="10" class="form-control"><?php echo htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></textarea>

<script>
    SkifRichTextEditor.init({
        elementId: <?php echo json_encode($editor_name, JSON_THROW_ON_ERROR); ?>,
        height: <?php echo $height; ?>,
        directory: <?php echo json_encode($directory, JSON_THROW_ON_ERROR); ?>,
        baseUrl: <?php echo json_encode(SkifPath::wrapUrlPath('/'), JSON_THROW_ON_ERROR); ?>,
        sourceEditorUrl: <?php echo json_encode(SkifPath::wrapAssetsVersion('/libraries/ace/ace.js'), JSON_THROW_ON_ERROR); ?>,
        styles: <?php echo json_encode(array_values($styles), JSON_THROW_ON_ERROR); ?>,
        fileManagerUrl: <?php echo json_encode($filemanager_path, JSON_THROW_ON_ERROR); ?>
    });
</script>
