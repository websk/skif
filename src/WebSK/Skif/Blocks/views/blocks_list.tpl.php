<?php

use WebSK\Skif\Blocks\Block;
use WebSK\Skif\Blocks\BlockUtils;
use WebSK\Skif\Blocks\ControllerBlocks;
use WebSK\Skif\Blocks\PageRegion;
use WebSK\Skif\Blocks\PageRegionsUtils;
use WebSK\Skif\SkifPhpRender;

echo SkifPhpRender::renderTemplateBySkifModule(
    'Blocks',
    'blocks_list_header.tpl.php'
);

$current_template_id = ControllerBlocks::getCurrentTemplateId();

$region_ids_arr = PageRegionsUtils::getPageRegionIdsArrByTemplateId($current_template_id);

foreach ($region_ids_arr as $page_region_id) {
    $page_region_obj = PageRegion::factory($page_region_id);

    $blocks_ids_arr = BlockUtils::getBlockIdsArrByPageRegionId($page_region_id, $current_template_id);
    ?>
    <h4><?php echo $page_region_obj->getTitle() ?></h4>

    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1 col-sm-1 col-xs-1">
            <col class="col-md-8 col-sm-6 col-xs-6">
            <col class="col-md-3 col-sm-5 col-xs-5">
        </colgroup>
        <?php
        foreach ($blocks_ids_arr as $block_id) {
            $block_obj = Block::factory($block_id);

            echo '<tr>';
            echo '<td>' . $block_obj->getId() . '</td>';
            echo '<td><a href="' . $block_obj->getEditorUrl() . '">' . $block_obj->getTitle() . '</td>';
            if ($page_region_id != Block::BLOCK_REGION_NONE) {
                ?>
                <td align="right">
                    <a href="<?php echo $block_obj->getEditorUrl(); ?>" title="Редактировать" class="btn btn-outline btn-default btn-sm">
                        <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
                    </a>
                    <a href="/admin/blocks?a=disable&amp;block_id=<?php echo $block_obj->getId(); ?>" title="Отключить" class="btn btn-outline btn-default btn-sm">
                        <span class="fa fa-power-off fa-lg text-muted fa-fw"></span>
                    </a>
                </td>
                <?php
            }
            echo '</tr>';
        }
        ?>
    </table>
    <?php
}

