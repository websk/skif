<?php
/**
 * @var int $block_id
 * @var BlockService $block_service
 */

use WebSK\Skif\Blocks\BlockService;
use WebSK\Logger\LoggerRender;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditorCachingHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditorChooseRegionHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditorContentHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditorDeleteHandler;
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditorPositionInRegionHandler;
use WebSK\Slim\Router;
use WebSK\Utils\Url;

$block_obj = $block_service->getById($block_id);
$current_url_no_query = Url::getUriNoQueryString();

$menu_arr = [
    ['link' => Router::urlFor(BlockEditorContentHandler::class, ['block_id' => $block_id]), 'name' => 'Содержимое и видимость'],
    ['link' => Router::urlFor(BlockEditorPositionInRegionHandler::class, ['block_id' => $block_id]), 'name' => 'Позиция'],
    ['link' => Router::urlFor(BlockEditorChooseRegionHandler::class, ['block_id' => $block_id]), 'name' => 'Регион'],
    ['link' => Router::urlFor(BlockEditorCachingHandler::class, ['block_id' => $block_id]), 'name' => 'Кэширование'],
    ['link' => Router::urlFor(BlockEditorDeleteHandler::class, ['block_id' => $block_id]), 'name' => 'Удаление блока'],
    [
        'link' => LoggerRender::getLoggerLinkForEntityObj($block_obj),
        'name' => 'Журнал <sup><span class="glyphicon glyphicon-new-window"></span></sup>', 'target' => '_blank'
    ],
];
?>
<div class="tabs">
    <ul class="nav nav-tabs">
        <?php
        foreach ($menu_arr as $menu_item_arr) {
            $class = ($current_url_no_query == $menu_item_arr['link']) ? ' active' : '';
            $target = array_key_exists('target', $menu_item_arr) ? 'target="' . $menu_item_arr['target'] .'""' : '';
            ?>
            <li <?php echo ( $class ? 'class="' . $class . '"' : ''); ?>>
                <a href="<?php echo $menu_item_arr['link']; ?>" <?php echo $target; ?>>
                    <?php echo $menu_item_arr['name']; ?>
                </a>
            </li>
            <?php
        }
        ?>
    </ul>
</div>
<p></p>