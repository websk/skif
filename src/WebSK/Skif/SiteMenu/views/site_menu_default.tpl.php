<?php
/**
 * @var int $site_menu_id
 */

use WebSK\Skif\SiteMenu\SiteMenuServiceProvider;
use WebSK\Slim\Container;
use WebSK\Utils\Url;

$container = Container::self();
$site_menu_item_service = SiteMenuServiceProvider::getSiteMenuItemService($container);

$site_menu_item_ids_arr = $site_menu_item_service->getIdsArrBySiteMenuId($site_menu_id);

$current_url_no_query = Url::getUriNoQueryString();
?>
<ul class="nav nav-stacked nav-pills nav-promote">
    <?php
    foreach ($site_menu_item_ids_arr as $site_menu_item_id) {
        $site_menu_item_obj = $site_menu_item_service->getById($site_menu_item_id);

        if (!$site_menu_item_obj->isPublished()) {
            continue;
        }
        ?>
        <li <?php echo (strpos($current_url_no_query, $site_menu_item_obj->getUrl()) !== false ? ' class="active"' : '') ?>><a href="<?php echo $site_menu_item_obj->getUrl();?>"><?php echo $site_menu_item_obj->getName(); ?></a></li>
    <?php
    }
    ?>
</ul>