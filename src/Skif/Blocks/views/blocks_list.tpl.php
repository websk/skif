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
            <col class="col-md-1 col-xs-1">
            <col class="col-md-9 col-xs-8">
            <col class="col-md-2 col-xs-3">
        </colgroup>
        <?php
        foreach ($blocks_ids_arr as $block_id) {
            $block_obj = \Skif\Blocks\Block::factory($block_id);

            echo '<tr>';
            echo '<td>' . $block_obj->getId() . '</td>';
            echo '<td><a href="' . $block_obj->getEditorUrl() . '">' . $block_obj->getTitle() . '</td>';
            if ($page_region_id != \Skif\Blocks\Block::BLOCK_REGION_NONE) {
                ?>
                <td align="right">
                    <a href="<?php echo $block_obj->getEditorUrl(); ?>" title="Редактировать"><span class="glyphicon glyphicon-edit text-primary font_percentage_120"></span></a>&nbsp;
                    <a href="/admin/blocks?a=disable&amp;block_id=<?php echo $block_obj->getId(); ?>" title="Отключить"><span class="glyphicon glyphicon-off text-muted font_percentage_120"></span></a>
                </td>
                <?php
            }
            echo '</tr>';
        }
        ?>
    </table>
    <?php
}

