<?php
/**
 * @var $message
 */

if ($message) {
    echo '<div class="alert alert-danger">'. $message .'</div>';
}

echo '<table class="table table-condensed table-striped">';

foreach ($blocks_arr as $block_id) {
    $block_obj = \Skif\Blocks\BlockFactory::loadBlockObj($block_id);
    if (!$block_obj) {
        continue;
    }

    echo '<tr>';
    echo '<td>' . $block_obj->getRegion() . '</td>';
    echo '<td>' . $block_obj->getId() . '</td>';
    echo '<td> ' . \Skif\Util\CHtml::link($block_obj->getInfo(), '/admin/blocks/edit/' . $block_id) . '</td>';
    echo '</tr>';
}
echo '</table>';