<?php
/**
 * @var $admin_nav_arr
 */

if (!isset($admin_nav_arr)) {
    return;
}

if (!\Skif\Users\AuthUtils::currentUserIsAdmin()) {
    return;
}

foreach ($admin_nav_arr as $admin_nav_link => $admin_nav_title) {
    ?>
    <div class="navbar navbar-default">
        <ul class="nav navbar-nav">
            <li>
                <a href="<?php echo $admin_nav_link; ?>" target="_blank"><?php echo $admin_nav_title; ?>&nbsp;<sup><span
                            class="glyphicon glyphicon-new-window"></span></sup></a>
            </li>
        </ul>
    </div>
<?php
}
