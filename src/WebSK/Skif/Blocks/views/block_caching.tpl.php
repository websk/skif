<?php
/**
 * @var int $block_id
 * @var BlockService $block_service
 */

use WebSK\Skif\Blocks\Block;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockSaveCachingHandler;
use WebSK\Slim\Router;
use WebSK\Views\PhpRender;

$block_obj = $block_service->getById($block_id);

echo PhpRender::renderLocalTemplate(
    'block_edit_menu.tpl.php',
    [
        'block_id' => $block_id,
        'block_service' => $this->block_service
    ]
);

$cache_contexts_arr = Block::BLOCK_CACHES_ARRAY;
?>
<form role="form" action="<?php echo Router::urlFor(BlockSaveCachingHandler::class, ['block_id' => $block_id]); ?>" method="post">
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