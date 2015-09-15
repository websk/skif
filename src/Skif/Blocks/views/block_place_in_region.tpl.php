<?php
/**
 * @var $block_id
 * @var $target_region
 */
$block_obj = \Skif\Blocks\ControllerBlocks::getBlockObj($block_id);
?>
<div class="tabs">
    <ul class="nav nav-tabs" style="margin-bottom: 10px;">
        <li><a href="/admin/blocks/edit/<?= $block_id ?>">Содержимое и видимость</a></li>
        <li class="active"><a href="/admin/blocks/edit/<?= $block_id ?>/position" class="active">Позиция</a></li>
        <li><a href="/admin/blocks/edit/<?= $block_id ?>/region">Регион</a></li>
        <li><a href="/admin/blocks/edit/<?= $block_id ?>/caching">Кэширование</a></li>
        <li><a href="/admin/blocks/edit/<?= $block_id ?>/delete">Удаление блока</a></li>
        <li><a href="/admin/logger/object_log/<?= urlencode(\Skif\Utils::getFullObjectId($block_obj));?>" target="_blank">Журнал</a></li>
    </ul>
</div>

<div class="tab-pane in active" id="place_in_region">
    <div class="container">
        <p>Выберите, после какого блока нужно поставить блок в регионе <?= $target_region ?></p>
        <?php
        $blocks_arr = \Skif\Blocks\ControllerBlocks::getBlocksIdsArrByTheme($block_obj->getTheme());
        usort($blocks_arr, array('Skif\Blocks\ControllerBlocks', '_block_compare'));
        ?>
        <table class="table table-condensed">
            <tr>
                <td>---</td>
                <td>---</td>
                <td> <?= \Skif\Util\CHtml::link('начало региона', \Skif\UrlManager::getUriNoQueryString() . '?_action=move_block&target_region=' . $target_region . '&target_weight=FIRST') ?></td>
            </tr>
            <?php
            foreach ($blocks_arr as $rblock) {
                if ($rblock['theme'] != $block_obj->getTheme()) {
                    continue;
                }

                if ($rblock['region'] != $target_region) {
                    continue;
                }

                $tr_class = '';

                if (\Skif\Blocks\ControllerBlocks::same_block($rblock, $block_obj)) {
                    $tr_class = ' class="active" ';
                }

                echo '<tr ' . $tr_class . '>';
                echo '<td>' . $rblock['weight'] . '</td>';
                echo '<td>' . $rblock['id'] . '</td>';
                echo '<td> ' . \Skif\Util\CHtml::link($rblock['info'], \Skif\UrlManager::getUriNoQueryString() . '?_action=move_block&target_region=' . $target_region . '&target_weight=' . $rblock['weight']) . '</td>';
                echo '</tr>';
            }
            ?>
        </table>
    </div>
</div>
