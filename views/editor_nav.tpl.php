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
<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#editor-navbar-collapse" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/admin"><span class="fa fa-wrench"></span></a>
        </div>
        <div class="collapse navbar-collapse" id="editor-navbar-collapse">
            <ul class="nav navbar-nav">
                <?php
                foreach ($nav_tabs_dto_arr as $nav_tab_item_dto) {
                    ?>
                    <li<?php echo (strpos($current_url_no_query, $nav_tab_item_dto->getUrl()) !== false ? ' class="active"' : '') ?>>
                        <a href="<?php echo $nav_tab_item_dto->getUrl(); ?>"><?php echo $nav_tab_item_dto->getName(); ?></a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
</nav>

