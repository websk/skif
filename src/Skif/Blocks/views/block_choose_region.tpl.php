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
<?php
$regions_arr = \Skif\Blocks\PageRegions::getRegionsArrByTheme($block_obj->getTheme());
$regions_arr[\Skif\Constants::BLOCK_REGION_NONE] = 'Выключенные блоки';
?>
<table class="table table-condensed">
    <?php
    foreach ($regions_arr as $region => $region_title) {
        $tr_class = '';

        if ($region == $block_obj->getRegion()) {
            $tr_class = ' class="active" ';
        }

        echo '<tr ' . $tr_class . '>';
        echo '<td><a href="' . $block_obj->getEditorUrl() . '/position?target_region=' . $region . '">' . $region_title .'</a></td>';
        echo '</tr>';
    }
    ?>
</table>
