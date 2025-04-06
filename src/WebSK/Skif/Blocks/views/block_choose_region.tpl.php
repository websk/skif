<?php
/**
 * @var int $block_id
 * @var BlockService $block_service
 * @var PageRegionService $page_region_service
 */

use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\PageRegionService;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditorPositionInRegionHandler;
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
?>

<p>Выберите регион, в котором нужно вывести блок:</p>

<table class="table table-condensed">
    <?php
    $region_ids_arr = $page_region_service->getIdsArrByTemplateId($block_obj->getTemplateId());

    foreach ($region_ids_arr as $page_region_id) {
        $page_region_obj = $page_region_service->getById($page_region_id);

        $tr_class = '';

        if ($page_region_id == $block_obj->getPageRegionId()) {
            $tr_class = ' class="active" ';
        }

        echo '<tr ' . $tr_class . '>';
        echo '<td><a href="' . Router::urlFor(
                BlockEditorPositionInRegionHandler::class,
                ['block_id' => $block_id],
                ['target_region' => $page_region_id]
            ) . '">' . $page_region_obj->getTitle() .'</a></td>';
        echo '</tr>';
    }
    ?>
</table>
