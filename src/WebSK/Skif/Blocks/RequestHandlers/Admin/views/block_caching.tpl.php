<?php
/**
 * @var int $block_id
 * @var BlockService $block_service
 */

use WebSK\Skif\Blocks\Block;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockSaveCachingHandler;
use WebSK\Skif\Router;
use WebSK\Views\PhpRender;

$block_obj = $block_service->getById($block_id);

echo PhpRender::renderLocalTemplate(
    'block_edit_menu.tpl.php',
    [
        'block_id' => $block_id,
        'block_service' => $block_service
    ]
);

$cache_contexts_arr = Block::BLOCK_CACHES_ARRAY;
?>
<form role="form" action="<?php echo Router::urlFor(BlockSaveCachingHandler::class, ['block_id' => $block_id]); ?>" method="post">
    <div class="mb-3">
        <label for="cache" class="form-label">Контекст кэширования</label>
        <select class="form-select" name="cache" id="cache">
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
    <div class="mb-3">
        <input class="btn btn-primary" type="submit" name="yt0" value="Сохранить" />
    </div>
</form>
