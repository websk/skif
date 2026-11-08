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
use WebSK\Skif\Blocks\RequestHandlers\Admin\BlockEditorPositionInRegionHandler;
use WebSK\Skif\Router;
use WebSK\Utils\Sanitize;
use WebSK\Utils\Url;

$block_obj = $block_service->getById($block_id);
$current_url_no_query = Url::getUriNoQueryString();

$menu_arr = [
    ['link' => Router::urlFor(BlockEditorContentHandler::class, ['block_id' => $block_id]), 'name' => 'Содержимое и видимость'],
    ['link' => Router::urlFor(BlockEditorChooseRegionHandler::class, ['block_id' => $block_id]), 'name' => 'Регион'],
    ['link' => Router::urlFor(BlockEditorPositionInRegionHandler::class, ['block_id' => $block_id]), 'name' => 'Позиция'],
    ['link' => Router::urlFor(BlockEditorCachingHandler::class, ['block_id' => $block_id]), 'name' => 'Кэширование'],
    [
        'link' => LoggerRender::getLoggerLinkForEntityObj($block_obj),
        'name' => 'Журнал', 'target' => '_blank', 'external' => true
    ],
];
?>
<div class="tabs">
    <ul class="nav nav-tabs">
        <?php
        foreach ($menu_arr as $menu_item_arr) {
            $is_active = $current_url_no_query == $menu_item_arr['link'];
            $target = array_key_exists('target', $menu_item_arr) ? ' target="' . Sanitize::sanitizeAttrValue($menu_item_arr['target']) . '" rel="noopener"' : '';
            ?>
            <li class="nav-item">
                <a class="nav-link<?php echo $is_active ? ' active' : ''; ?>" href="<?php echo Sanitize::sanitizeAttrValue($menu_item_arr['link']); ?>"<?php echo $target; ?><?php echo $is_active ? ' aria-current="page"' : ''; ?>>
                    <?php echo Sanitize::sanitizeTagContent($menu_item_arr['name']); ?>
                    <?php if (!empty($menu_item_arr['external'])) { ?><span class="fa fa-external-link" aria-hidden="true"></span><?php } ?>
                </a>
            </li>
            <?php
        }
        ?>
    </ul>
</div>
<p></p>
