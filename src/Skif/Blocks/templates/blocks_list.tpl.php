<?php

$theme = \Skif\Blocks\ControllerBlocks::getEditorTheme();

$regions_arr = \Skif\Blocks\PageRegions::getRegionsArrByTheme($theme);
$regions_arr[-1] = 'Отключенные блоки';

$blocks_arr = \Skif\Blocks\ControllerBlocks::getBlocksIdsArrByTheme($theme);
usort($blocks_arr, array('Skif\Blocks\ControllerBlocks', '_block_compare'));

foreach ($regions_arr as $region => $region_title) {
    ?>
    <p><span class="label label-default"><?php echo $region_title ?></p>

    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1">
            <col class="col-md-10">
            <col class="col-md-1">
        </colgroup>
        <?php
        foreach ($blocks_arr as $rblock) {
            if ($rblock['region'] != $region) {
                continue;
            }
            ?>
            <tr>
                <td><span class="text-muted"><?php echo $rblock['id']; ?></span></td>
                <td>
                    <a href="/admin/blocks/edit/<?php echo $rblock['id']; ?>"><?php echo $rblock['info']; ?></a>
                </td>
                <td align="right">
                    <?php
                    if ($region != -1) {
                        ?>
                        <a href="/admin/blocks/list?a=disable&block_id=<?php echo $rblock['id']; ?>" title="Отключить">
                            <span class="glyphicon glyphicon-off"></span>
                        </a>
                    <?php
                    }
                    ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>
<?php
}
