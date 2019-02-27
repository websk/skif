<?php
/**
 * @var $block_id
 */

use WebSK\Skif\Blocks\BlockUtils;
use WebSK\Skif\Blocks\ControllerBlocks;
use WebSK\Views\PhpRender;

$block_obj = ControllerBlocks::getBlockObj($block_id);

echo PhpRender::renderLocalTemplate(
    'block_edit_menu.tpl.php',
    array('block_id' => $block_id)
);

if (!$block_obj->isLoaded()) {
    echo '<div class="alert alert-warning">Во время создания блока вкладка недоступна.</div>';
    return;
}

$cache_contexts_arr = BlockUtils::getCachesArr();
?>
<form role="form" action="<?php echo $block_obj->getEditorUrl(); ?>/caching" method="post">
    <input type="hidden" value="save_caching" name="_action" id="_action" />
    <div class="form-group">
        <label for="cache">Контекст кэширования</label>
        <select class="form-control" name="cache" id="cache">
            <?php
            foreach ($cache_contexts_arr as $cache_id => $cache_name) {
                ?>
                <option value="<?php echo $cache_id; ?>"<?php echo ($block_obj->getCache() == $cache_id ? ' selected="selected"' : '')?>>
                    <?php echo $cache_name; ?>
                </option>
                <?php
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <input class="btn btn-primary" type="submit" name="yt0" value="Сохранить" />
    </div>
</form>