<?php
echo \Skif\PhpTemplate::renderTemplateBySkifModule(
    'Blocks',
    'blocks_list_header.tpl.php'
);

$template_id = \Skif\Blocks\ControllerBlocks::getCurrentTemplateId();

$region_ids_arr = \Skif\Blocks\PageRegionsUtils::getPageRegionIdsArrByTemplateId($template_id);

foreach ($region_ids_arr as $page_region_id) {
    $page_region_obj = \Skif\Blocks\PageRegion::factory($page_region_id);

    $blocks_ids_arr = \Skif\Blocks\BlockUtils::getBlockIdsArrByPageRegionId($page_region_id);
    ?>
    <h4><?php echo $page_region_obj->getTitle() ?></h4>

    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1">
            <col class="col-md-10">
            <col class="col-md-1">
        </colgroup>
        <?php
        foreach ($blocks_ids_arr as $block_id) {
            $block_obj = \Skif\Blocks\Block::factory($block_id);

            echo '<tr>';
            echo '<td>' . $block_obj->getId() . '</td>';
            echo '<td><a href="' . $block_obj->getEditorUrl() . '">' . $block_obj->getTitle() . ' <span class="glyphicon glyphicon-edit text-warning"></span></a></td>';
            if ($page_region_id != \Skif\Blocks\Block::BLOCK_REGION_NONE) {
                echo '<td align="right"> ';
                echo '<a class="glyphicon glyphicon-off text-warning" href="/admin/blocks?a=disable&amp;block_id=' . $block_obj->getId() . '" title="Отключить"></a>';
                echo '</td>';
            }
            echo '</tr>';
        }
        ?>
    </table>
    <?php
}

