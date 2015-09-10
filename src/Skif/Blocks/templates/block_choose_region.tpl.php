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
        <li class="active"><a href="/admin/blocks/edit/<?= $block_id ?>/region" class="active">Регион</a></li>
        <li><a href="/admin/blocks/edit/<?= $block_id ?>/caching">Кэширование</a></li>
        <li><a href="/admin/blocks/edit/<?= $block_id ?>/delete">Удаление блока</a></li>
        <li><a href="/admin/logger/object_log/<?= urlencode(\Skif\Utils::getFullObjectId($block_obj));?>" target="_blank">Журнал</a></li>
    </ul>
</div>

<p>Выберите регион, в котором нужно вывести блок:</p>
<?php
$regions_arr = \Skif\Blocks\PageRegions::getRegionsArrByTheme($block_obj->getTheme());
$regions_arr['-1'] = 'Выключенные блоки';
?>
<table class="table table-condensed">
    <?php
    foreach ($regions_arr as $region => $region_title) {
        $tr_class = '';

        if ($region == $block_obj->getRegion()) {
            $tr_class = ' class="active" ';
        }

        echo '<tr ' . $tr_class . '>';
        echo '<td>' . \Skif\Util\CHtml::link($region_title, '/' . \Skif\Blocks\ControllerBlocks::getEditorUrl($block_id) . '/position' . "?target_region=" . $region) . '</td>';
        echo '</tr>';
    }
    ?>
</table>
