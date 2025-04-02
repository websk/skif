<?php
/**
 * @var int $block_id
 * @var ?int $target_region
 */

use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\ControllerBlocks;
use WebSK\Skif\Blocks\PageRegionService;
use WebSK\Slim\Container;
use WebSK\Utils\Url;
use WebSK\Views\PhpRender;

$container = Container::self();
$page_region_service = $container->get(PageRegionService::class);
$block_service = $container->get(BlockService::class);

$block_obj = ControllerBlocks::getBlockObj($block_id);

echo PhpRender::renderLocalTemplate(
    'block_edit_menu.tpl.php',
    array('block_id' => $block_id)
);

if (!$block_obj->isLoaded()) {
    echo '<div class="alert alert-warning">Во время создания блока вкладка недоступна.</div>';
    return;
}

$page_region_obj = $page_region_service->getById($target_region);

?>

<div class="tab-pane in active" id="place_in_region">
    <div>
        <p>Выберите, после какого блока нужно поставить блок в регионе &laquo;<?= $page_region_obj->getTitle() ?>&raquo;</p>
        <table class="table table-condensed">
            <tr>
                <td>---</td>
                <td>---</td>
                <td><a href="<?php echo Url::getUriNoQueryString() . '?_action=move_block&target_region=' . $target_region . '&target_weight=FIRST'; ?>">начало региона</a></td>
            </tr>
            <?php
            $blocks_ids_arr = $block_service->getBlockIdsArrByPageRegionId($target_region, $block_obj->getTemplateId());

            foreach ($blocks_ids_arr as $other_block_id) {
                $other_block_obj = $block_service->getById($other_block_id);

                $tr_class = '';
                if ($other_block_obj->getId() == $block_obj->getId()) {
                    $tr_class = ' class="active" ';
                }

                $move_block_url = Url::getUriNoQueryString() .
                    '?_action=move_block&target_region=' . $target_region . '&target_weight=' . $other_block_obj->getWeight();

                echo '<tr ' . $tr_class . '>';
                echo '<td>' . $other_block_obj->getWeight() . '</td>';
                echo '<td>' . $other_block_obj->getId() . '</td>';
                echo '<td><a href="' . $move_block_url . '">' . $other_block_obj->getTitle() . '</a></td>';
                echo '</tr>';
            }
            ?>
        </table>
    </div>
</div>