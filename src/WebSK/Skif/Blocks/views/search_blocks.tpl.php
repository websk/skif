<?php
/**
 * @var string $search_value
 * @var array $block_ids_arr
 * @var int $current_template_id
 * @var BlockService $block_service
 * @var PageRegionService $page_region_service
 * @var TemplateService $template_service
 */

use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\PageRegionService;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditorContentHandler;
use WebSK\Skif\Content\TemplateService;
use WebSK\Slim\Router;
use WebSK\Views\PhpRender;

echo PhpRender::renderLocalTemplate(
    'blocks_list_header.tpl.php',
    [
        'current_template_id' => $current_template_id,
        'search_value' => $search_value,
        'template_service' => $template_service
    ]
);
?>
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1 col-xs-1">
            <col class="col-md-8 col-xs-7">
            <col class="col-md-3 col-xs-4">
        </colgroup>

<?php
foreach ($block_ids_arr as $block_id) {
    $block_obj = $block_service->getById($block_id);

    $page_region_obj = $page_region_service->getById($block_obj->getPageRegionId());

    echo '<tr>';
    echo '<td>' . $block_obj->getId() . '</td>';
    echo '<td><a href="' . Router::urlFor(BlockEditorContentHandler::class, ['block_id' => $block_id]) . '">' . $block_obj->getTitle() . ' <span class="glyphicon glyphicon-edit text-warning"></span></a></td>';
    echo '<td>' . $page_region_obj->getTitle() . '</td>';
    echo '</tr>';
}
echo '</table>';