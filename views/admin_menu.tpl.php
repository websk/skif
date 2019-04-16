<?php

use WebSK\Config\ConfWrapper;
use WebSK\Utils\Url;

$current_url_no_query = Url::getUriNoQueryString();

$admin_menu_arr = ConfWrapper::value('admin_menu');
?>

<ul class="sidebar-menu">

<?php
foreach ($admin_menu_arr as $menu_item_arr) {
    $class = ($current_url_no_query == $menu_item_arr['link']) ? ' active' : '';
    $target = array_key_exists('target', $menu_item_arr) ? 'target="' . $menu_item_arr['target'] . '""' : '';
    ?>
    <li <?php echo($class ? 'class="' . $class . '"' : ''); ?>>
        <a href="<?php echo $menu_item_arr['link']; ?>" <?php echo $target; ?>>
            <?php
            if (array_key_exists('icon', $menu_item_arr)) {
                echo $menu_item_arr['icon'];
            }
            ?>
            <span><?php echo $menu_item_arr['name']; ?></span>
        </a>
        <?php
        if (array_key_exists('sub_menu', $menu_item_arr)) {
            ?>
            <ul class="treeview-menu">
                <?php
                foreach ($menu_item_arr['sub_menu'] as $sub_menu_item_arr) {
                    $target = array_key_exists('target', $sub_menu_item_arr) ? 'target="' . $sub_menu_item_arr['target'] . '""' : '';
                    ?>
                    <li>
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
