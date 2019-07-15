<?php
/**
 * @var array[] $admin_menu_arr
 */

use WebSK\Utils\Url;

$current_url_no_query = Url::getUriNoQueryString();
?>

<ul class="sidebar-menu" data-widget="tree">
    <?php
    foreach ($admin_menu_arr as $menu_item_arr) {
        $class = ($current_url_no_query == $menu_item_arr['link']) ? ' active' : '';
        $target = array_key_exists('target', $menu_item_arr) ? 'target="' . $menu_item_arr['target'] . '""' : '';

        $has_submenu = array_key_exists('sub_menu', $menu_item_arr);

        if ($has_submenu) {
            $class .= ' treeview';
            $ul_active = '';
            foreach ($menu_item_arr['sub_menu'] as $sub_menu_item_arr) {
                if ($current_url_no_query != $sub_menu_item_arr['link']) {
                    continue;
                }
                $ul_active = ' menu-open ';
                $class .= ' active';
                break;
            }
        }
        ?>
        <li<?php echo($class ? ' class="' . $class . '"' : ''); ?>>
            <a href="<?php echo $menu_item_arr['link']; ?>" <?php echo $target; ?>>
                <?php
                if (array_key_exists('icon', $menu_item_arr)) {
                    echo $menu_item_arr['icon'];
                }
                ?>
                <span><?php echo $menu_item_arr['name']; ?></span>
                <?php
                if ($has_submenu) {
                    ?>
                    <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
                    <?php
                }
                ?>
            </a>
            <?php
            if ($has_submenu) {
                ?>
                <ul class="treeview-menu<?php echo $ul_active ?>">
                    <?php
                    foreach ($menu_item_arr['sub_menu'] as $sub_menu_item_arr) {
                        $class = ($current_url_no_query == $sub_menu_item_arr['link']) ? ' active' : '';
                        $target = array_key_exists('target', $sub_menu_item_arr) ? 'target="' . $sub_menu_item_arr['target'] . '""' : '';
                        ?>
                        <li<?php echo($class ? ' class="' . $class . '"' : ''); ?>>
                            <a href="<?php echo $sub_menu_item_arr['link']; ?>" <?php echo $target; ?>>
                                <?php
                                if (array_key_exists('icon', $sub_menu_item_arr)) {
                                    echo $sub_menu_item_arr['icon'];
                                }
                                ?>
                                <span><?php echo $sub_menu_item_arr['name']; ?></span>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <?php
            }
            ?>
        </li>
        <?php
    }
    ?>
</ul>
