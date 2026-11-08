<?php
/**
 * @var NavTabItemDTO[] $nav_tabs_dto_arr
 */

use WebSK\Auth\Auth;
use WebSK\Utils\Sanitize;
use WebSK\Utils\Url;
use WebSK\Views\NavTabItemDTO;

if (empty($nav_tabs_dto_arr)) {
    return;
}

if (!Auth::currentUserIsAdmin()) {
    return;
}

$current_url_no_query = Url::getUriNoQueryString();
?>
<nav class="mb-3" aria-label="Действия редактора">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item"><a class="nav-link" href="/admin" aria-label="Администрирование"><i class="fa fa-wrench" aria-hidden="true"></i></a></li>
        <?php
        foreach ($nav_tabs_dto_arr as $nav_tab_item_dto) {
            ?>
            <?php $is_active = str_contains($current_url_no_query, $nav_tab_item_dto->getUrl()); ?>
            <li class="nav-item">
                <a class="nav-link<?php echo $is_active ? ' active' : ''; ?>" href="<?php echo Sanitize::sanitizeAttrValue($nav_tab_item_dto->getUrl()); ?>"<?php echo $nav_tab_item_dto->getTarget() ? ' target="' . Sanitize::sanitizeAttrValue($nav_tab_item_dto->getTarget()) . '" rel="noopener"' : ''; ?><?php echo $is_active ? ' aria-current="page"' : ''; ?>><?php echo Sanitize::sanitizeTagContent($nav_tab_item_dto->getName()); ?></a>
            </li>
            <?php
        }
        ?>
    </ul>
</nav>

