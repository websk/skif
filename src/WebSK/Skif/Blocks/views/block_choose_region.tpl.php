<?php
/**
 * @var int $block_id
 */

use WebSK\Skif\Blocks\BlockRoutes;
use WebSK\Skif\Blocks\BlockUtils;
use WebSK\Skif\Blocks\PageRegionService;
use WebSK\Slim\Container;
use WebSK\Views\PhpRender;

$container = Container::self();
$page_region_service = $container->get(PageRegionService::class);

$block_obj = BlockUtils::getBlockObj($block_id);

echo PhpRender::renderLocalTemplate(
    'block_edit_menu.tpl.php',
    [
        'block_id' => $block_id,
        'block_service' => $this->block_service
    ]
);

if (!$block_obj->isLoaded()) {
    echo '<div class="alert alert-warning">Во время создания блока вкладка недоступна.</div>';
    return;
}
?>

<p>Выберите регион, в котором нужно вывести блок:</p>

<table class="table table-condensed">
    <?php
    $region_ids_arr = $page_region_service->getPageRegionIdsArrByTemplateId($block_obj->getTemplateId());

    foreach ($region_ids_arr as $page_region_id) {
        $page_region_obj = $page_region_service->getById($page_region_id);

        $tr_class = '';

        if ($page_region_id == $block_obj->getPageRegionId()) {
            $tr_class = ' class="active" ';
        }

        echo '<tr ' . $tr_class . '>';
        echo '<td><a href="' . BlockRoutes::getEditorUrl($block_id) . '/position?target_region=' . $page_region_id . '">' . $page_region_obj->getTitle() .'</a></td>';
        echo '</tr>';
    }
    ?>
</table>
