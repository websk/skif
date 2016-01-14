<?php
/**
 * @var $editor_nav_arr
 */
if (!isset($editor_nav_arr)) {
    return;
}

$current_url_no_query = \Skif\UrlManager::getUriNoQueryString();
?>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#editor-navbar-collapse" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><span class="fa fa-wrench"></span></a>
        </div>
        <div class="collapse navbar-collapse" id="editor-navbar-collapse">
            <ul class="nav navbar-nav">
                <?php
                foreach ($editor_nav_arr as $editor_nav_link => $editor_nav_title) {
                    ?>
                    <li<?php echo (strpos($current_url_no_query, $editor_nav_link) !== false ? ' class="active"' : '') ?>>
                        <a href="<?php echo $editor_nav_link; ?>" target="_blank"><?php echo $editor_nav_title; ?></a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
</nav>

