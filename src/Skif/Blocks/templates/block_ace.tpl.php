<?php
/**
 * @var $block_id
 */

$block_obj = \Skif\Blocks\ControllerBlocks::getBlockObj($block_id);
?>
    <div class="tabs">
        <ul class="nav nav-tabs" style="margin-bottom: 10px;">
            <li><a href="/admin/blocks/edit/<?= $block_id ?>">Содержимое и видимость</a></li>
            <li><a href="/admin/blocks/edit/<?= $block_id ?>/position">Позиция</a></li>
            <li><a href="/admin/blocks/edit/<?= $block_id ?>/region">Регион</a></li>
            <li><a href="/admin/blocks/edit/<?= $block_id ?>/caching">Кэширование</a></li>
            <li><a href="/admin/blocks/edit/<?= $block_id ?>/delete">Удаление блока</a></li>
            <li><a href="/admin/logger/object_log/<?= urlencode(\Skif\Utils::getFullObjectId($block_obj));?>" target="_blank">Журнал</a></li>
            <li class="active"><a href="/admin/blocks/edit/<?= $block_id ?>/ace" class="active">ACE</a></li>
        </ul>
    </div>

    <script src="/js/ace/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>

<?php
echo \Skif\Util\CHtml::beginForm(\Skif\UrlManager::getUriNoQueryString(), 'post', array(
    'role' => 'form',
    'id' => 'edit_form'
));

echo \Skif\Util\TB::formGroup(\Skif\Util\CHtml::label('Код блока', false) .  '<div id="editor"></div>');
echo \Skif\Util\CHtml::hiddenField('_action', 'save_ace') . "\n";
echo \Skif\Util\CHtml::hiddenField('body') . "\n";

// Обычная кнопка submit;
echo \Skif\Util\CHtml::submitButton('Сохранить', array(
    'class' => 'btn btn-default save-btn-js'
));

echo \Skif\Util\CHtml::endForm();

$block_body_for_js = json_encode($block_obj->getBody());
?>

<style type="text/css" media="screen">
    .ace_editor {
        height: 500px;
        font-size: 14px;
    }
</style>
<script>
    var editor = ace.edit("editor");
    var form = $('#edit_form');
    editor.setTheme("ace/theme/monokai");
    editor.getSession().setMode("ace/mode/javascript");
    editor.setValue(<?php echo $block_body_for_js; ?>);
    editor.getSession().setUseWrapMode(true);

    $('.save-btn-js').on('click', function(event) {
        event.preventDefault;

        $('#body').val(editor.getValue());
        form.submit();
    });
</script>
