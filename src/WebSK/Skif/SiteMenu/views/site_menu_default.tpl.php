<?php
/**
 * @var int $site_menu_id
 */

use WebSK\Skif\SiteMenu\SiteMenuServiceProvider;
use WebSK\Skif\SiteMenu\SiteMenuPathMatcher;
use WebSK\Skif\TemplateSanitize;
use WebSK\Slim\Container;
use WebSK\Utils\Sanitize;
use WebSK\Utils\Url;

$container = Container::self();
$site_menu_item_service = SiteMenuServiceProvider::getSiteMenuItemService($container);

$site_menu_item_ids_arr = $site_menu_item_service->getIdsArrBySiteMenuId($site_menu_id);

$current_url_no_query = Url::getUriNoQueryString();
?>
<ul class="nav flex-column nav-pills gap-1 nav-promote">
    <?php
    foreach ($site_menu_item_ids_arr as $site_menu_item_id) {
        $site_menu_item_obj = $site_menu_item_service->getById($site_menu_item_id);

        if (!$site_menu_item_obj->isPublished()) {
            continue;
        }
        ?>
        <?php $is_active = SiteMenuPathMatcher::isActive($current_url_no_query, $site_menu_item_obj->getUrl()); ?>
        <li class="nav-item">
            <a class="nav-link<?php echo $is_active ? ' active' : ''; ?>"
               href="<?php echo TemplateSanitize::urlAttribute($site_menu_item_obj->getUrl()); ?>"
                <?php echo $is_active ? 'aria-current="page"' : ''; ?>><?php echo Sanitize::sanitizeTagContent($site_menu_item_obj->getName()); ?></a>
        </li>
    <?php
    }
    ?>
</ul>
