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
        <li class="active"><a href="/admin/blocks/edit/<?= $block_id ?>/delete" class="active">Удаление блока</a></li>
        <li><a href="/admin/logger/object_log/<?= urlencode(\Skif\Utils::getFullObjectId($block_obj));?>" target="_blank">Журнал</a></li>
    </ul>
</div>
<div class="tab-pane in active" id="place_in_region">
    <div class="container">
        <p class="alert alert-danger">Внимание! Блок будет безвозвратно удален!  (Включая все содержимое и расположение блока).</p>
        <p class="alert alert-info">Если Вы хотите <b>отключить блок</b> - поместите его в регион <a href="/admin/blocks/edit/<?= $block_id ?>/region">Выключенные блоки</a></p>
        <?php
        echo \Skif\Util\CHtml::beginForm(\Skif\UrlManager::getUriNoQueryString(), 'post', array('role' => 'form'));
        echo \Skif\Util\CHtml::hiddenField('_action', 'delete_block');

        $items = array();

        $items[] = \Skif\Util\CHtml::submitButton('Удалить блок', array('class' => "btn btn-default"));

        foreach ($items as $item) {
            echo '<div class="form-group">' . $item . '</div>';
        }

        echo \Skif\Util\CHtml::endForm();
        ?>
    </div>
</div>
