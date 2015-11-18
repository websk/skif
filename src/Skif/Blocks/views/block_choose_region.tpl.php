<?php
/**
 * @var $block_id
 */

$block_obj = \Skif\Blocks\ControllerBlocks::getBlockObj($block_id);

echo \Skif\PhpTemplate::renderTemplateBySkifModule(
    'Blocks',
    'block_edit_menu.tpl.php',
    array('block_id' => $block_id)
);

if (!$block_obj->isLoaded()) {
    echo '<div class="alert alert-warning">Во время создания блока вкладка недоступна.</div>';
    return;
}
?>

<p>Выберите регион, в котором нужно вывести блок:</p>

<table class="table table-condensed">
    <?php
    $region_ids_arr = \Skif\Blocks\PageRegionsUtils::getPageRegionIdsArrByTemplateId($block_obj->getTemplateId());

    foreach ($region_ids_arr as $page_region_id) {
        $page_region_obj = \Skif\Blocks\PageRegion::factory($page_region_id);

        $tr_class = '';

        if ($page_region_id == $block_obj->getPageRegionId()) {
            $tr_class = ' class="active" ';
        }

        echo '<tr ' . $tr_class . '>';
        echo '<td><a href="' . $block_obj->getEditorUrl() . '/position?target_region=' . $page_region_id . '">' . $page_region_obj->getTitle() .'</a></td>';
        echo '</tr>';
    }
    ?>
</table>
