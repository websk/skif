<?php
/**
 * @var \WebSK\Views\NavTabItemDTO[] $nav_tabs_dto_arr
 */

use WebSK\Auth\Auth;
use WebSK\Utils\Url;

if (empty($nav_tabs_dto_arr)) {
    return;
}

if (!Auth::currentUserIsAdmin()) {
    return;
}

$current_url_no_query = Url::getUriNoQueryString();
?>
<div>
    <ul class="nav nav-tabs">
        <li role="presentation"><a href="/admin"><i class="fa fa-wrench"></i></a></li>
        <?php
        foreach ($nav_tabs_dto_arr as $nav_tab_item_dto) {
            ?>
            <li role="presentation" <?php echo (strpos($current_url_no_query, $nav_tab_item_dto->getUrl()) !== false ? ' class="active"' : '') ?>>
                <a href="<?php echo $nav_tab_item_dto->getUrl(); ?>"<?php echo $nav_tab_item_dto->getTarget() ? 'target="' . $nav_tab_item_dto->getTarget() . '"' : ''; ?>><?php echo $nav_tab_item_dto->getName(); ?></a>
            </li>
            <?php
        }
        ?>
    </ul>
</div>

