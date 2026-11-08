<?php
/**
 * @var int $parent_item_id
 */

use WebSK\Skif\SiteMenu\SiteMenuServiceProvider;
use WebSK\Skif\SiteMenu\SiteMenuPathMatcher;
use WebSK\Skif\TemplateSanitize;
use WebSK\Slim\Container;
use WebSK\Utils\Sanitize;
use WebSK\Utils\Url;

$container = Container::self();
$site_menu_item_service = SiteMenuServiceProvider::getSiteMenuItemService($container);

$current_url_no_query = Url::getUriNoQueryString();
$current_site_menu_item_id = $site_menu_item_service->getCurrentId();

$parent_item_obj = $site_menu_item_service->getById($parent_item_id);
$children_ids_arr = $site_menu_item_service->getChildrenIdsArr($parent_item_obj);

if (!$children_ids_arr) {
    return '';
}

$descendants_ids_arr = $site_menu_item_service->getDescendantsIdsArr($parent_item_obj);
if (!in_array($current_site_menu_item_id, $descendants_ids_arr)) {
    return '';
}
?>
<ul class="nav flex-column nav-pills gap-1">
    <?php
    foreach ($children_ids_arr as $children_site_menu_item_id) {
        $children_site_menu_item_obj = $site_menu_item_service->getById($children_site_menu_item_id);

        if (!$children_site_menu_item_obj->isPublished()) {
            continue;
        }

        $is_active = SiteMenuPathMatcher::isActive($current_url_no_query, $children_site_menu_item_obj->getUrl());
        ?>
        <li class="nav-item">
            <a class="nav-link<?php echo $is_active ? ' active' : ''; ?>"
               href="<?php echo TemplateSanitize::urlAttribute($children_site_menu_item_obj->getUrl()); ?>"
                <?php echo $is_active ? 'aria-current="page"' : ''; ?>><?php echo Sanitize::sanitizeTagContent($children_site_menu_item_obj->getName()); ?></a>
        </li>
        <?php
    }
    ?>
</ul>

