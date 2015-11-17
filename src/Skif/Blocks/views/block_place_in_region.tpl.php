<?php
/**
 * @var $block_id
 * @var $target_region
 */

$block_obj = \Skif\Blocks\ControllerBlocks::getBlockObj($block_id);

echo \Skif\PhpTemplate::renderTemplateBySkifModule(
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
    <div class="container">
        <p>Выберите, после какого блока нужно поставить блок в регионе <?= $target_region ?></p>
        <table class="table table-condensed">
            <tr>
                <td>---</td>
                <td>---</td>
                <td><a href="<?php echo \Skif\UrlManager::getUriNoQueryString() . '?_action=move_block&target_region=' . $target_region . '&target_weight=FIRST'; ?>">начало региона</a></td>
            </tr>
            <?php
            $blocks_ids_arr = \Skif\Blocks\BlockUtils::getBlocksIdsArrInRegion($target_region, $block_obj->getTheme());

            foreach ($blocks_ids_arr as $other_block_id) {
                $other_block_obj = \Skif\Blocks\Block::factory($other_block_id);

                $tr_class = '';
                if ($other_block_obj->getId() == $block_obj->getId()) {
                    $tr_class = ' class="active" ';
                }

                $move_block_url = \Skif\UrlManager::getUriNoQueryString() .
                    '?_action=move_block&target_region=' . $target_region . '&target_weight=' . $other_block_obj->getWeight();

                echo '<tr ' . $tr_class . '>';
                echo '<td>' . $other_block_obj->getWeight() . '</td>';
                echo '<td>' . $other_block_obj->getId() . '</td>';
                echo '<td><a href="' . $move_block_url . '">' . $other_block_obj->getInfo() . '</a></td>';
                echo '</tr>';
            }
            ?>
        </table>
    </div>
</div>