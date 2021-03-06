<?php
/**
 * @var $parent_item_id
 */

use WebSK\Skif\SiteMenu\SiteMenuItem;
use WebSK\Skif\SiteMenu\SiteMenuUtils;
use WebSK\Utils\Url;

$current_url_no_query = Url::getUriNoQueryString();
$current_site_menu_item_id = SiteMenuUtils::getCurrentSiteMenuItemId();

$parent_item_obj = SiteMenuItem::factory($parent_item_id);
$children_ids_arr = $parent_item_obj->getChildrenIdsArr();

if (!$children_ids_arr) {
    return '';
}

$descendants_ids_arr = $parent_item_obj->getDescendantsIdsArr();
if (!in_array($current_site_menu_item_id, $descendants_ids_arr)) {
    return '';
}
?>
<ul>
    <?php
    foreach ($children_ids_arr as $children_site_menu_item_id) {
        $children_site_menu_item_obj = SiteMenuItem::factory($children_site_menu_item_id);

        if (!$children_site_menu_item_obj->isPublished()) {
            continue;
        }

        $class = strpos($current_url_no_query, $children_site_menu_item_obj->getUrl()) !== false ? ' class="active"' : '';
        ?>
        <li<?php echo $class; ?>>
            <a href="<?php echo $children_site_menu_item_obj->getUrl(); ?>"><?php echo $children_site_menu_item_obj->getName(); ?></a>
        </li>
        <?php
    }
    ?>
</ul>

