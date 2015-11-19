<?php
/**
 * @var $title
 * @var $breadcrumbs
 * @var $content
 */

$user_id = \Skif\Users\AuthUtils::getCurrentUserId();

if (!$user_id || !\Skif\Users\AuthUtils::currentUserIsAdmin()) {
    $content = '<h2>Вход в систему управления</h2>';
    $content .= \Skif\PhpTemplate::renderTemplateBySkifModule(
        'Users',
        'login_form.tpl.php',
        array('destination' => '/admin')
    );
}
?>
<!DOCTYPE html>
<html lang="ru">
<head xmlns:og="http://ogp.me/ns#">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>СКИФ - Система управления сайтом</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <script type="text/javascript" src="/vendor/bower/jquery/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="/vendor/bower/jquery-ui/themes/base/jquery-ui.min.css">
    <script type="text/javascript" src="/vendor/bower/jquery-ui/jquery-ui.min.js"></script>

    <link type="text/css" rel="stylesheet" media="all" href="/vendor/bower/bootstrap/dist/css/bootstrap.min.css"/>
    <script type="text/javascript" src="/vendor/bower/bootstrap/dist/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="/vendor/websk/skif/assets/css/admin.css" type="text/css">

    <script type="text/javascript" src="/vendor/bower/jquery-validation/dist/jquery.validate.min.js"></script>

    <script type="text/javascript" src="/vendor/bower/fancybox/source/jquery.fancybox.pack.js"></script>
    <link rel="stylesheet" type="text/css" href="/vendor/bower/fancybox/source/jquery.fancybox.css" media="screen"/>

    <script type="text/javascript" src="/vendor/bower/moment/min/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="/vendor/bower/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <link type="text/css" rel="stylesheet" media="all" href="/vendor/bower/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css"/>

    <script type="text/javascript" src="/vendor/ckeditor/ckeditor/ckeditor.js"></script>
</head>
<body>
<?php
if (\Skif\Users\AuthUtils::currentUserIsAdmin()) {
    ?>
    <div class="navbar navbar-default navbar-static-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav nav-pills">
                    <?php
                    $current_url_no_query = \Skif\UrlManager::getUriNoQueryString();

                    $config_admin_menu_arr = \Skif\Conf\ConfWrapper::value('admin_menu');

                    $admin_menu_arr = array(
                        '/admin/content/page' => 'Страницы',
                        '/admin/site_menu' => 'Менеджер меню',
                        '/admin/content/news' => 'Новости',
                        '/admin/users' => 'Пользователи',
                        '/admin/blocks' => 'Блоки',
                        '/admin/redirect/list' => 'Редиректы',
                        '/admin/key_value' => 'Переменные',
                    );

                    $admin_menu_arr = array_merge($config_admin_menu_arr, $admin_menu_arr);

                    foreach ($admin_menu_arr as $item_url => $item_title) {
                        ?>
                        <li <?php echo(strpos($current_url_no_query, $item_url) !== false ? 'class="active"' : '') ?>>
                            <a href="<?php echo $item_url; ?>"><?php echo $item_title; ?></a>
                        </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
<?php
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <a href="/admin/">
                <img src="/vendor/websk/skif/assets/images/admin/logo.gif" border="0" width="250" height="86" alt="СКИФ" class="img-responsive">
            </a>
        </div>
        <div class="col-md-9">
            <?php
            if (\Skif\Users\AuthUtils::currentUserIsAdmin()) {
                $user_obj = \Skif\Users\User::factory($user_id);
                ?>
                <div>
                    <a href="/admin/users/edit/<?= $user_id ?>"
                       class="label label-default"><?= $user_obj->getName() ?></a>
                    <a href="/user/logout" class="label label-default">Выход</a> /
                    <a href="/" target="_blank"
                       class="label label-primary"><b><?php echo \Skif\Conf\ConfWrapper::value('site_name'); ?></b></a>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <div class="page-header">
        <?php
        if (!isset($breadcrumbs_arr)) {
            $breadcrumbs_arr = array();
        }

        $breadcrumbs_arr = array_merge(
            array('Главная' => '/admin'),
            $breadcrumbs_arr
        );

        echo \Skif\PhpTemplate::renderTemplate('/breadcrumbs.tpl.php', array('breadcrumbs_arr' => $breadcrumbs_arr));

        if (!empty($title)) {
            echo '<h1>' . $title . '</h1>';
        }

        echo \Skif\Messages::renderMessages();
        ?>
    </div>

    <div><?php echo $content; ?></div>

    <p></p>
    <hr>

    <div class="row">
        <div class="col-md-8 col-xs-8">
            <a href="<?php echo \Skif\Conf\ConfWrapper::value('site_url'); ?>"
               target="_blank"><?php echo \Skif\Conf\ConfWrapper::value('site_name'); ?></a>
        </div>
        <div class="col-md-4 col-xs-4" align="right">
            <a href="http://www.websk.ru" target="_blank" title="Система управления сайтом СКИФ / websk.ru">
                <img src="/vendor/websk/skif/assets/images/admin/skif.gif" alt="СКИФ" border="0" width="88" height="30">
            </a>
        </div>
    </div>
    <p></p>
</div>

</body>
</html>