<?php
/**
 * @var $message
 * @var $block_ids_arr
 */

$search_value = $_POST['search'];

echo \Skif\PhpTemplate::renderTemplateBySkifModule(
    'Blocks',
    'blocks_list_header.tpl.php',
    array('search_value' => $search_value)
);
?>
    <table class="table table-striped table-hover">
        <colgroup>
            <col class="col-md-1">
            <col class="col-md-10">
            <col class="col-md-1">
        </colgroup>

<?php
foreach ($block_ids_arr as $block_id) {
    $block_obj = \Skif\Blocks\Block::factory($block_id);

    echo '<tr>';
    echo '<td>' . $block_obj->getId() . '</td>';
    echo '<td><a href="' . $block_obj->getEditorUrl() . '">' . $block_obj->getInfo() . ' <span class="glyphicon glyphicon-edit text-warning"></span></a></td>';
    echo '<td>' . $block_obj->getRegion() . '</td>';
    echo '</tr>';
}
echo '</table>';