<?php
/**
 * @var $message
 * @var $block_ids_arr
 */

use WebSK\Skif\Blocks\Block;
use WebSK\Skif\Blocks\PageRegion;
use WebSK\Views\PhpRender;

$search_value = $_POST['search'];

echo PhpRender::renderLocalTemplate(
    'blocks_list_header.tpl.php',
    array('search_value' => $search_value)
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
    $block_obj = Block::factory($block_id);

    $page_region_obj = PageRegion::factory($block_obj->getPageRegionId());

    echo '<tr>';
    echo '<td>' . $block_obj->getId() . '</td>';
    echo '<td><a href="' . $block_obj->getEditorUrl() . '">' . $block_obj->getTitle() . ' <span class="glyphicon glyphicon-edit text-warning"></span></a></td>';
    echo '<td>' . $page_region_obj->getTitle() . '</td>';
    echo '</tr>';
}
echo '</table>';