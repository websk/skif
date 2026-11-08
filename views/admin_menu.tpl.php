<?php
/**
 * @var array[] $admin_menu_arr
 */

use WebSK\Utils\Url;
use WebSK\Utils\Sanitize;
use WebSK\Skif\TemplateSanitize;

$current_url_no_query = Url::getUriNoQueryString();
$render_icon = static function (mixed $icon): string {
    if (!is_string($icon)) {
        return '';
    }

    if (preg_match('/class=["\']([^"\']+)["\']/', $icon, $matches)) {
        $icon = $matches[1];
    }

    $classes = preg_replace('/[^a-zA-Z0-9 _-]/', '', $icon);
    if (!$classes) {
        return '';
    }

    return '<i class="nav-icon ' . Sanitize::sanitizeAttrValue($classes) . '" aria-hidden="true"></i>';
};
?>
<ul id="admin-sidebar-menu" class="nav sidebar-menu flex-column" data-lte-toggle="treeview" data-accordion="false" role="menu">
    <?php foreach ($admin_menu_arr as $menu_item_arr) { ?>
        <?php
        $has_submenu = array_key_exists('sub_menu', $menu_item_arr);
        $is_active = $current_url_no_query == $menu_item_arr['link'];
        $submenu_is_active = false;
        if ($has_submenu) {
            foreach ($menu_item_arr['sub_menu'] as $sub_menu_item_arr) {
                if ($current_url_no_query == $sub_menu_item_arr['link']) {
                    $submenu_is_active = true;
                    break;
                }
            }
        }
        $target = array_key_exists('target', $menu_item_arr) ? ' target="' . Sanitize::sanitizeAttrValue($menu_item_arr['target']) . '" rel="noopener"' : '';
        ?>
        <li class="nav-item<?php echo $submenu_is_active ? ' menu-open' : ''; ?>">
            <?php if ($has_submenu) { ?>
            <button type="button" class="nav-link w-100 border-0<?php echo($submenu_is_active ? ' active' : ''); ?>" aria-label="<?php echo Sanitize::sanitizeAttrValue($menu_item_arr['name']); ?>">
            <?php } else { ?>
            <a href="<?php echo TemplateSanitize::urlAttribute($menu_item_arr['link']); ?>" class="nav-link<?php echo($is_active ? ' active' : ''); ?>" aria-label="<?php echo Sanitize::sanitizeAttrValue($menu_item_arr['name']); ?>"<?php echo $target; ?>>
            <?php } ?>
                <?php if (array_key_exists('icon', $menu_item_arr)) { echo $render_icon($menu_item_arr['icon']); } ?>
                <p>
                    <?php echo Sanitize::sanitizeTagContent($menu_item_arr['name']); ?>
                    <?php if ($has_submenu) { ?><i class="nav-arrow fa fa-angle-right" aria-hidden="true"></i><?php } ?>
                </p>
            <?php echo $has_submenu ? '</button>' : '</a>'; ?>
            <?php if ($has_submenu) { ?>
                <ul class="nav nav-treeview">
                    <?php foreach ($menu_item_arr['sub_menu'] as $sub_menu_item_arr) { ?>
                        <?php
                        $sub_is_active = $current_url_no_query == $sub_menu_item_arr['link'];
                        $sub_target = array_key_exists('target', $sub_menu_item_arr) ? ' target="' . Sanitize::sanitizeAttrValue($sub_menu_item_arr['target']) . '" rel="noopener"' : '';
                        ?>
                        <li class="nav-item">
                            <a href="<?php echo TemplateSanitize::urlAttribute($sub_menu_item_arr['link']); ?>" class="nav-link<?php echo $sub_is_active ? ' active' : ''; ?>" aria-label="<?php echo Sanitize::sanitizeAttrValue($sub_menu_item_arr['name']); ?>"<?php echo $sub_target; ?>>
                                <?php if (array_key_exists('icon', $sub_menu_item_arr)) { echo $render_icon($sub_menu_item_arr['icon']); } ?>
                                <p><?php echo Sanitize::sanitizeTagContent($sub_menu_item_arr['name']); ?></p>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </li>
    <?php } ?>
</ul>
