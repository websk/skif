<?php
/**
 * @var $title
 * @var $breadcrumbs
 * @var $content
 */

$user_id = \Skif\Users\AuthUtils::getCurrentUserId();

if (!$user_id || !\Skif\Users\AuthUtils::currentUserIsAdmin()) {
    echo \Skif\PhpTemplate::renderTemplate(
        'layouts/layout.admin_login.tpl.php'
    );

    return;
}

$user_obj = \Skif\Users\User::factory($user_id);
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>СКИФ - Система управления сайтом</title>

    <script src="/vendor/bower/jquery/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="/vendor/bower/jquery-ui/themes/base/jquery-ui.min.css">
    <script type="text/javascript" src="/vendor/bower/jquery-ui/jquery-ui.min.js"></script>

    <!-- Bootstrap Core CSS -->
    <link href="/vendor/bower/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="/vendor/bower/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- MetisMenu CSS -->
    <link href="/vendor/bower/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/vendor/websk/skif/assets/libraries/sb-admin-2/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="/vendor/bower/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="/vendor/websk/skif/assets/css/admin.css" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script type="text/javascript" src="/vendor/bower/jquery-validation/dist/jquery.validate.min.js"></script>

    <script type="text/javascript" src="/vendor/bower/fancybox/source/jquery.fancybox.pack.js"></script>
    <link rel="stylesheet" type="text/css" href="/vendor/bower/fancybox/source/jquery.fancybox.css" media="screen"/>

    <script type="text/javascript" src="/vendor/bower/moment/min/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="/vendor/bower/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <link type="text/css" rel="stylesheet" media="all" href="/vendor/bower/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css"/>

    <script type="text/javascript" src="/vendor/ckeditor/ckeditor/ckeditor.js"></script>
</head>

<body>

<div id="wrapper">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/admin">
                <img src="/vendor/websk/skif/assets/images/admin/skif.gif" alt="СКИФ" border="0" height="28" title="Система управления сайтом СКИФ / websk.ru" class="img-responsive">
            </a>
        </div>
        <!-- /.navbar-header -->

        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a href="/" target="_blank">
                    <i class="fa fa-external-link fa-fw"></i>
                </a>
            </li>

            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li></li>
                    <li><a href="/admin/users/edit/<?php echo $user_id; ?>"><i class="fa fa-user fa-fw"></i> <?php echo $user_obj->getName(); ?></a>
                    </li>
                    <li class="divider"></li>
                    <li><a href="/user/logout?destination=/admin"><i class="fa fa-sign-out fa-fw"></i> Выход</a>
                    </li>
                </ul>
                <!-- /.dropdown-user -->
            </li>
        </ul>
        <!-- /.navbar-top-links -->

        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <?php
                    $current_url_no_query = \Skif\UrlManager::getUriNoQueryString();

                    $config_admin_menu_arr = \Skif\Conf\ConfWrapper::value('admin_menu');

                    $admin_menu_arr = array(
                        array('link' => '/admin/content/page', 'name' => 'Страницы', 'icon' => '<i class="fa fa-files-o fa-fw"></i>'),
                        array('link' => '/admin/site_menu', 'name' => 'Менеджер меню', 'icon' => '<i class="fa fa-bars fa-fw"></i>'),
                        array('link' => '/admin/content/news', 'name' => 'Новости', 'icon' => '<i class="fa fa-newspaper-o fa-fw"></i>'),
                        array('link' => '/admin/users', 'name' => 'Пользователи', 'icon' => '<i class="fa fa-users fa-fw"></i>'),
                        array('link' => '/admin/blocks', 'name' => 'Блоки', 'icon' => '<i class="fa fa-table fa-fw"></i>'),
                    );

                    $admin_menu_arr = array_merge($admin_menu_arr, $config_admin_menu_arr);

                    foreach ($admin_menu_arr as $menu_item_arr) {

                        $class = ($current_url_no_query == $menu_item_arr['link']) ? ' active' : '';
                        $target = array_key_exists('target', $menu_item_arr) ? 'target="' . $menu_item_arr['target'] .'""' : '';
                        ?>
                        <li <?php echo ( $class ? 'class="' . $class . '"' : ''); ?>>
                            <a href="<?php echo $menu_item_arr['link']; ?>" <?php echo $target; ?>>
                                <?php
                                if (array_key_exists('icon', $menu_item_arr)) {
                                    echo $menu_item_arr['icon'];
                                }
                                ?>
                                <?php echo $menu_item_arr['name']; ?>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                    <li>
                        <a href="#"><i class="fa fa-wrench fa-fw"></i> Настройки<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="/admin/redirect/list">Редиректы</a>
                            </li>
                            <li>
                                <a href="/admin/key_value">Переменные</a>
                            </li>
                        </ul>
                        <!-- /.nav-second-level -->
                    </li>
                </ul>
            </div>
            <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
    </nav>

    <!-- Page Content -->
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div>
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
                            echo '<h1 class="page-header">' . $title . '</h1>';
                        }

                        echo \Skif\Messages::renderMessages();
                        ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div><?php echo $content; ?></div>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- Metis Menu Plugin JavaScript -->
<script src="/vendor/bower/metisMenu/dist/metisMenu.min.js"></script>

<!-- Custom Theme JavaScript -->
<script src="/vendor/websk/skif/assets/libraries/sb-admin-2/js/sb-admin-2.js"></script>

</body>

</html>
