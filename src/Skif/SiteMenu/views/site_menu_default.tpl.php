<?php
/**
 * @var $site_menu_id
 */

use WebSK\Utils\Url;

$site_menu_item_ids_arr = \Skif\SiteMenu\SiteMenuUtils::getSiteMenuItemIdsArr($site_menu_id);

$current_url_no_query = Url::getUriNoQueryString();
?>
<ul class="nav nav-stacked nav-pills nav-promote">
    <?php
    foreach ($site_menu_item_ids_arr as $site_menu_item_id) {
        $site_menu_type_obj = \Skif\SiteMenu\SiteMenuItem::factory($site_menu_item_id);

        if (!$site_menu_type_obj->isPublished()) {
            continue;
        }
        ?>
        <li <?php echo (strpos($current_url_no_query, $site_menu_type_obj->getUrl()) !== false ? ' class="active"' : '') ?>><a href="<?php echo $site_menu_type_obj->getUrl();?>"><?php echo $site_menu_type_obj->getName(); ?></a></li>
    <?php
    }
    ?>
</ul>