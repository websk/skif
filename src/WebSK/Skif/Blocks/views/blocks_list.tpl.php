<?php
/**
 * @var int $current_template_id
 * @var BlockService $block_service
 * @var PageRegionService $page_region_service
 * @var TemplateService $template_service
 */

use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\PageRegion;
use WebSK\Skif\Blocks\PageRegionService;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockDisableHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditorContentHandler;
use WebSK\Skif\Content\TemplateService;
use WebSK\Slim\Router;
use WebSK\Views\PhpRender;

echo PhpRender::renderLocalTemplate(
    'blocks_list_header.tpl.php',
    [
        'current_template_id' => $current_template_id,
        'search_value' => '',
        'template_service' => $template_service
    ]
);

$region_ids_arr = $page_region_service->getIdsArrByTemplateId($current_template_id);

?>
<table class="table table-striped table-hover">
    <colgroup>
        <col class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
        <col class="col-lg-8 col-md-8 col-sm-6 col-xs-6">
        <col class="col-lg-3 col-md-3 col-sm-5 col-xs-5">
    </colgroup>
    <tbody>

    <?php
    foreach ($region_ids_arr as $page_region_id) {
        $page_region_obj = $page_region_service->getById($page_region_id);

        $blocks_ids_arr = $block_service->getIdsArrByPageRegionId($page_region_id, $current_template_id);
        ?>
        <tr>
            <th colspan="3"><?php echo $page_region_obj->getTitle(); ?></th>
        </tr>
        <?php
        foreach ($blocks_ids_arr as $block_id) {
            $block_obj = $block_service->getById($block_id);

            echo '<tr>';
            echo '<td style="max-width: 30px">' . $block_obj->getId() . '</td>';
            echo '<td><a href="' . Router::urlFor(BlockEditorContentHandler::class, ['block_id' => $block_id]) . '">' . $block_obj->getTitle() . '</td>';
            if ($page_region_id != PageRegion::BLOCK_REGION_NONE) {
                ?>
                <td align="right">
                    <a href="<?php echo Router::urlFor(BlockEditorContentHandler::class, ['block_id' => $block_id]); ?>" title="Редактировать"
                       class="btn btn-default btn-sm">
                        <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
                    </a>
                    <a href="<?php echo Router::urlFor(BlockDisableHandler::class, ['block_id' => $block_id]); ?>"
                       title="Отключить" class="btn btn-default btn-sm">
                        <span class="fa fa-power-off fa-lg text-muted fa-fw"></span>
                    </a>
                </td>
                <?php
            }
            echo '</tr>';
        }
        ?>
        <?php
    }
    ?>
    </tbody>
</table>

