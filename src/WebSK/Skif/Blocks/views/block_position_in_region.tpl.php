<?php
/**
 * @var int $block_id
 * @var int $target_region
 * @var BlockService $block_service
 * @var PageRegionService $page_region_service
 */

use WebSK\Skif\Blocks\Block;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\PageRegionService;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockChangePositionInRegionHandler;
use WebSK\Slim\Router;
use WebSK\Views\PhpRender;


$block_obj = $block_service->getById($block_id);

echo PhpRender::renderLocalTemplate(
    'block_edit_menu.tpl.php',
    [
        'block_id' => $block_id,
        'block_service' => $this->block_service
    ]
);

$page_region_obj = $page_region_service->getById($target_region);

?>

<div class="tab-pane in active" id="place_in_region">
    <div>
        <p>Выберите, после какого блока нужно поставить блок в регионе
            &laquo;<?php echo $page_region_obj->getTitle() ?>&raquo;</p>
        <table class="table table-condensed">
            <tr>
                <td>---</td>
                <td>---</td>
                <td>
                    <a href="<?php echo Router::urlFor(
                        BlockChangePositionInRegionHandler::class,
                        ['block_id' => $block_id],
                        ['target_region' => $target_region, 'target_weight' => Block::BLOCK_WEIGHT_FIRST_IN_REGION]
                    ); ?>">
                        начало региона
                    </a>
                </td>
            </tr>
            <?php
            $blocks_ids_arr = $block_service->getIdsArrByPageRegionId($target_region, $block_obj->getTemplateId());

            foreach ($blocks_ids_arr as $other_block_id) {
                $other_block_obj = $block_service->getById($other_block_id);

                $tr_class = '';
                if ($other_block_obj->getId() == $block_obj->getId()) {
                    $tr_class = ' class="active" ';
                }

                $move_block_url = Router::urlFor(
                    BlockChangePositionInRegionHandler::class,
                    ['block_id' => $block_id],
                    ['target_region' => $target_region, 'target_weight' => $other_block_obj->getWeight()]
                );

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