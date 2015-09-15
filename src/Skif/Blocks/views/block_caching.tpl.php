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
        <li class="active"><a href="/admin/blocks/edit/<?= $block_id ?>/caching" class="active">Кэширование</a></li>
        <li><a href="/admin/blocks/edit/<?= $block_id ?>/delete">Удаление блока</a></li>
        <li><a href="/admin/logger/object_log/<?= urlencode(\Skif\Utils::getFullObjectId($block_obj));?>" target="_blank">Журнал</a></li>
    </ul>
</div>
<?php
echo \Skif\Util\CHtml::beginForm(\Skif\UrlManager::getUriNoQueryString(), 'post', array('role' => 'form'));
echo \Skif\Util\CHtml::hiddenField('_action', 'save_caching');

$items = array();

$cache_contexts_arr = array(
    -1 => 'не кэшировать',
    1 => 'кэшировать для каждой роли',
    2 => 'кэшировать для каждого пользователя',
    4 => 'кэшировать для каждого урла',
    8 => 'кэшировать глобально'
);

$items[] = \Skif\Util\CHtml::label('Контекст кэширования', 'cache') .
    \Skif\Util\CHtml::dropDownList('cache', $block_obj->getCache(), $cache_contexts_arr,
        array('class' => "form-control"));

$items[] = \Skif\Util\CHtml::submitButton('Сохранить', array('class' => "btn btn-default"));

foreach ($items as $item) {
    echo '<div class="form-group">' . $item . '</div>';
}

echo \Skif\Util\CHtml::endForm();
?>
</div>
