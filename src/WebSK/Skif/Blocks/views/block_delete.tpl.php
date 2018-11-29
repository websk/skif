<?php
/**
 * @var $block_id
 */

use WebSK\Skif\Blocks\ControllerBlocks;
use WebSK\Skif\SkifPhpRender;

$block_obj = ControllerBlocks::getBlockObj($block_id);

echo SkifPhpRender::renderTemplateBySkifModule(
    'Blocks',
    'block_edit_menu.tpl.php',
    array('block_id' => $block_id)
);

if (!$block_obj->isLoaded()) {
    echo '<div class="alert alert-warning">Во время создания блока вкладка недоступна.</div>';
    return;
}
?>

<div class="tab-pane in active" id="place_in_region">
    <div>
        <p class="alert alert-danger">Внимание! Блок будет безвозвратно удален!  (Включая все содержимое и расположение блока).</p>
        <p class="alert alert-info">Если Вы хотите <b>отключить блок</b> - поместите его в регион <a href="<?php echo $block_obj->getEditorUrl() ?>/region">Выключенные блоки</a></p>

        <form role="form" action="<?php echo $block_obj->getEditorUrl() ?>/delete" method="post">
            <input type="hidden" value="delete_block" name="_action" id="_action" />
            <div class="form-group">
                <input class="btn btn-danger" type="submit" name="yt0" value="Удалить блок" />
            </div>
        </form>
    </div>
</div>
