<?php

use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\BlockUtils;
use WebSK\Skif\Blocks\PageRegion;
use WebSK\Skif\Blocks\PageRegionService;
use WebSK\Slim\Container;
use WebSK\Views\PhpRender;

echo PhpRender::renderLocalTemplate(
    'blocks_list_header.tpl.php'
);

$container = Container::self();
$page_region_service = $container->get(PageRegionService::class);
$block_service = $container->get(BlockService::class);

$current_template_id = BlockUtils::getCurrentTemplateId();

$region_ids_arr = $page_region_service->getPageRegionIdsArrByTemplateId($current_template_id);

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

        $blocks_ids_arr = $block_service->getBlockIdsArrByPageRegionId($page_region_id, $current_template_id);
        ?>
        <tr>
            <th colspan="3"><?php echo $page_region_obj->getTitle(); ?></th>
        </tr>
        <?php
        foreach ($blocks_ids_arr as $block_id) {
            $block_obj = $block_service->getById($block_id);

            echo '<tr>';
            echo '<td style="max-width: 30px">' . $block_obj->getId() . '</td>';
            echo '<td><a href="' . $block_obj->getEditorUrl() . '">' . $block_obj->getTitle() . '</td>';
            if ($page_region_id != PageRegion::BLOCK_REGION_NONE) {
                ?>
                <td align="right">
                    <a href="<?php echo $block_obj->getEditorUrl(); ?>" title="Редактировать"
                       class="btn btn-default btn-sm">
                        <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
                    </a>
                    <a href="/admin/blocks?a=disable&amp;block_id=<?php echo $block_obj->getId(); ?>"
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

