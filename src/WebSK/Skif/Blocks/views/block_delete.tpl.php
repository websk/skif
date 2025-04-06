<?php
/**
 * @var int $block_id
 * @var BlockService $block_service
 */

use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditorChooseRegionHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockDeleteHandler;
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
?>

<div class="tab-pane in active" id="place_in_region">
    <div>
        <p class="alert alert-danger">Внимание! Блок будет безвозвратно удален!  (Включая все содержимое и расположение блока).</p>
        <p class="alert alert-info">Если Вы хотите <b>отключить блок</b> - поместите его в регион <a href="<?php echo Router::urlFor(BlockEditorChooseRegionHandler::class, ['block_id' => $block_id]); ?>">Выключенные блоки</a></p>

        <form role="form" action="<?php echo Router::urlFor(BlockDeleteHandler::class, ['block_id' => $block_id]); ?>" method="post">
            <div class="form-group">
                <input class="btn btn-danger" type="submit" name="yt0" value="Удалить блок" />
            </div>
        </form>
    </div>
</div>
