<?php
/**
 * @var int $block_id
 * @var BlockService $block_service
 */

use WebSK\Skif\Blocks\BlockRoutes;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Logger\LoggerRender;
use WebSK\Utils\Url;

$block_obj = $block_service->getById($block_id);
$current_url_no_query = Url::getUriNoQueryString();

$block_edit_url = BlockRoutes::getEditorUrl($block_id);

$menu_arr = array(
    array('link' => $block_edit_url, 'name' => 'Содержимое и видимость'),
    array('link' => $block_edit_url . '/position', 'name' => 'Позиция'),
    array('link' => $block_edit_url . '/region', 'name' => 'Регион'),
    array('link' => $block_edit_url . '/caching', 'name' => 'Кэширование'),
    array('link' => $block_edit_url . '/delete', 'name' => 'Удаление блока'),
    array(
        'link' => LoggerRender::getLoggerLinkForEntityObj($block_obj),
        'name' => 'Журнал <sup><span class="glyphicon glyphicon-new-window"></span></sup>', 'target' => '_blank'
    ),
);
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