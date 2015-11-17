<?php
echo \Skif\PhpTemplate::renderTemplateBySkifModule(
    'Blocks',
    'blocks_list_header.tpl.php'
);

$theme = \Skif\Blocks\ControllerBlocks::getEditorTheme();

$regions_arr = \Skif\Blocks\PageRegions::getRegionsArrByTheme($theme);
$regions_arr[\Skif\Constants::BLOCK_REGION_NONE] = 'Отключенные блоки';

foreach ($regions_arr as $region => $region_title) {
    $blocks_ids_arr = \Skif\Blocks\BlockUtils::getBlocksIdsArrInRegion($region, $theme);

    ?>
    <h4><?php echo $region_title ?></h4>

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
            echo '<td><a href="' . $block_obj->getEditorUrl() . '">' . $block_obj->getInfo() . ' <span class="glyphicon glyphicon-edit text-warning"></span></a></td>';

            if ($region != \Skif\Constants::BLOCK_REGION_NONE) {
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
