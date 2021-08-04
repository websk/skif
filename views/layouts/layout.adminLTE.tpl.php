<?php
/**
 * @var LayoutDTO $layout_dto
 */

use WebSK\Auth\Auth;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\User\UserRoutes;
use WebSK\Config\ConfWrapper;
use WebSK\Image\ImageManager;
use WebSK\Image\ImagePresets;
use WebSK\Skif\SkifApp;
use WebSK\Skif\SkifPath;
use WebSK\Slim\Router;
use WebSK\Utils\Messages;
use WebSK\Utils\Url;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

$user_obj = Auth::getCurrentUserObj();
if (!$user_obj) {
    return '';
}

if (!isset($layout_dto)) {
    /**
     * @var string $title
     * @var string $content
     * @var array $breadcrumbs_arr
     */

    $layout_dto = new LayoutDTO();
    $layout_dto->setTitle($title);
    $layout_dto->setContentHtml($content);

    $breadcrumbs_dto_arr = [
        new BreadcrumbItemDTO('Главная', Router::pathFor(SkifApp::ROUTE_NAME_ADMIN))
    ];

    if (!empty($breadcrumbs_arr)) {
        foreach ($breadcrumbs_arr as $breadcrumb_title => $breadcrumb_link) {
            $breadcrumbs_dto_arr[] = new BreadcrumbItemDTO($breadcrumb_title, $breadcrumb_link);
        }
    }
    $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_dto_arr);
}

$user_name = $user_obj->getName();
$user_photo_path = ImageManager::getImgUrlByPreset($user_obj->getPhotoPath(), ImagePresets::IMAGE_PRESET_200_auto);
$user_email = $user_obj->getEmail();

$user_edit_uri = Router::pathFor(UserRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_obj->getId()]);
$user_logout_url = Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGOUT, [], ['destination' => Router::pathFor(SkifApp::ROUTE_NAME_ADMIN)]);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>СКИФ. <?php echo $layout_dto->getTitle() ?></title>

    <link href="<?php echo SkifPath::wrapUrlPath('/favicon.ico'); ?>" rel="shortcut icon" type="image/x-icon">

    <!-- jQuery -->
    <script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery/jquery.min.js'); ?>"></script>
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery-ui/themes/base/jquery-ui.min.css'); ?>" rel="stylesheet" type="text/css">
    <script type="text/javascript"
            src="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery-ui/jquery-ui.min.js'); ?>"></script>

    <!-- Bootstrap -->
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css">
    <script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap/js/bootstrap.min.js'); ?>"></script>

    <!-- MetisMenu CSS -->
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/metisMenu/metisMenu.min.css'); ?>" rel="stylesheet" type="text/css">

    <!-- Ionicons -->
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/ionicons/ionicons.min.css'); ?>" rel="stylesheet" type="text/css">

    <!-- AdminLTE -->
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/AdminLTE/css/AdminLTE.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/AdminLTE/css/skin-blue.min.css'); ?>" rel="stylesheet" type="text/css">

    <!-- Font Awesome -->
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet" type="text/css">

    <link href="<?php echo SkifPath::wrapAssetsVersion('/styles/skif.css'); ?>" rel="stylesheet" type="text/css">

    <!-- Jquery Validate -->
    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery-validation/jquery.validate.min.js'); ?>"></script>

    <!-- Fancybox -->
    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/fancybox/jquery.fancybox.min.js"'); ?>></script>
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/fancybox/jquery.fancybox.min.css'); ?>" rel="stylesheet" type="text/css">

    <!-- Moment -->
    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/moment/moment.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/moment/moment.ru.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'); ?>"></script>

    <!-- Bootstrap datetimepicker -->
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css'); ?>" rel="stylesheet" type="text/css">

    <!-- CKEditor -->
    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/ckeditor/ckeditor.js'); ?>"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/html5shiv/html5shiv.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/respond.js/respond.min.js'); ?>"></script>
    <![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini ">

<div class="wrapper">

    <!-- Main Header -->
    <header class="main-header">

        <!-- Logo -->
        <a href="<?php echo Router::pathFor(SkifApp::ROUTE_NAME_ADMIN); ?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini">
                <span class="logo-lg" title="Система управления сайтом СКИФ / websk.ru"><b>СК</b></span>
            </span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg">
                <span class="logo-lg" title="Система управления сайтом СКИФ / websk.ru"><b>СКИФ</b></span>
            </span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- The user image in the navbar-->
                            <?php
                            if ($user_obj->getPhoto()) {
                                ?>
                                <img src="<?php echo $user_photo_path ?>" class="user-image" alt="User Image">
                            <?php
                            } else {
                                ?>
                                <svg viewBox="0 0 50 50" width="25px" height="25px" style="display: block;"
                                     class="user-image">
                                    <circle cx="25" cy="25" fill="none" r="24" stroke="#ffffff"
                                            stroke-linecap="round" stroke-miterlimit="10" stroke-width="2"/>
                                    <rect fill="none" height="50" width="50"/>
                                    <path fill="#ffffff"
                                          d="M29.933,35.528c-0.146-1.612-0.09-2.737-0.09-4.21c0.73-0.383,2.038-2.825,2.259-4.888c0.574-0.047,1.479-0.607,1.744-2.818  c0.143-1.187-0.425-1.855-0.771-2.065c0.934-2.809,2.874-11.499-3.588-12.397c-0.665-1.168-2.368-1.759-4.581-1.759  c-8.854,0.163-9.922,6.686-7.981,14.156c-0.345,0.21-0.913,0.878-0.771,2.065c0.266,2.211,1.17,2.771,1.744,2.818  c0.22,2.062,1.58,4.505,2.312,4.888c0,1.473,0.055,2.598-0.091,4.21c-1.261,3.39-7.737,3.655-11.473,6.924  c3.906,3.933,10.236,6.746,16.916,6.746s14.532-5.274,15.839-6.713C37.688,39.186,31.197,38.93,29.933,35.528z"/>
                                </svg>
                            <?php
                            }
                            ?>
                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span class="hidden-xs"><?php echo $user_name; ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header">
                                <?php
                                if ($user_obj->getPhoto()) {
                                    ?>
                                    <img src="<?php echo $user_photo_path ?>" class="img-circle" alt="User Image">
                                <?php
                                } else {
                                    ?>
                                    <svg viewBox="0 0 50 50" width="90px" height="90px"
                                         style="display: block;margin: auto;">
                                        <circle cx="25" cy="25" fill="none" r="24" stroke="#ffffff"
                                                stroke-linecap="round" stroke-miterlimit="10" stroke-width="2"/>
                                        <rect fill="none" height="50" width="50"/>
                                        <path fill="#ffffff"
                                              d="M29.933,35.528c-0.146-1.612-0.09-2.737-0.09-4.21c0.73-0.383,2.038-2.825,2.259-4.888c0.574-0.047,1.479-0.607,1.744-2.818  c0.143-1.187-0.425-1.855-0.771-2.065c0.934-2.809,2.874-11.499-3.588-12.397c-0.665-1.168-2.368-1.759-4.581-1.759  c-8.854,0.163-9.922,6.686-7.981,14.156c-0.345,0.21-0.913,0.878-0.771,2.065c0.266,2.211,1.17,2.771,1.744,2.818  c0.22,2.062,1.58,4.505,2.312,4.888c0,1.473,0.055,2.598-0.091,4.21c-1.261,3.39-7.737,3.655-11.473,6.924  c3.906,3.933,10.236,6.746,16.916,6.746s14.532-5.274,15.839-6.713C37.688,39.186,31.197,38.93,29.933,35.528z"/>
                                    </svg>
                                <?php
                                }
                                ?>
                                <p><?= $user_name ?></p>
                            </li>

                            <li class="user-body">
                                <div class="row">
                                    <div class="col-xs-12 text-center">
                                        <a href="/" target="_blank"><span class="glyphicon glyphicon-new-window"></span> Перейти на сайт</a>
                                    </div>
                                </div>
                            </li>

                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?php echo $user_edit_uri ?>" class="btn btn-default btn-flat">Мой профиль</a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?php echo $user_logout_url ?>"
                                       class="btn btn-default btn-flat">Выход</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

            <!-- Sidebar user panel (optional) -->
            <div class="user-panel">
                <div class="pull-left image">
                    <?php
                    if ($user_obj->getPhoto()) {
                        ?>
                        <img src="<?php echo $user_photo_path ?>" class="img-circle" alt="User Image">
                    <?php
                    } else {
                        ?>
                        <svg viewBox="0 0 50 50" width="45px" height="45px" style="display: block;"
                             class="user-ico">
                            <circle cx="25" cy="25" fill="none" r="24" stroke="#b8c7ce" stroke-linecap="round"
                                    stroke-miterlimit="10" stroke-width="2"/>
                            <rect fill="none" height="50" width="50"/>
                            <path fill="#b8c7ce"
                                  d="M29.933,35.528c-0.146-1.612-0.09-2.737-0.09-4.21c0.73-0.383,2.038-2.825,2.259-4.888c0.574-0.047,1.479-0.607,1.744-2.818  c0.143-1.187-0.425-1.855-0.771-2.065c0.934-2.809,2.874-11.499-3.588-12.397c-0.665-1.168-2.368-1.759-4.581-1.759  c-8.854,0.163-9.922,6.686-7.981,14.156c-0.345,0.21-0.913,0.878-0.771,2.065c0.266,2.211,1.17,2.771,1.744,2.818  c0.22,2.062,1.58,4.505,2.312,4.888c0,1.473,0.055,2.598-0.091,4.21c-1.261,3.39-7.737,3.655-11.473,6.924  c3.906,3.933,10.236,6.746,16.916,6.746s14.532-5.274,15.839-6.713C37.688,39.186,31.197,38.93,29.933,35.528z"/>
                        </svg>
                    <?php
                    }
                    ?>
                </div>
                <div class="pull-left info">
                    <p><a href="<?php echo $user_edit_uri ?>"><?= $user_email ?></a></p>
                    <!-- Status -->
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <?php
            ?>

            <!-- Sidebar Menu -->
            <?php
            echo PhpRender::renderLocalTemplate(
                '../admin_menu.tpl.php',
                ['admin_menu_arr' => SkifPath::getMenuArr()]
            );
            ?>
            <!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?php echo $layout_dto->getTitle() ?>
            </h1>
            <?php
            echo PhpRender::renderLocalTemplate(
                '../breadcrumbs.tpl.php',
                ['breadcrumbs_dto_arr' => $layout_dto->getBreadcrumbsDtoArr()]
            );
            ?>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php
                    echo Messages::renderMessages();
                    ?>

                    <?php
                    if ($layout_dto->getNavTabsDtoArr()) {
                        $current_url_no_query = Url::getUriNoQueryString();
                        ?>
                        <div>
                            <ul class="nav nav-tabs">
                                <?php
                                foreach ($layout_dto->getNavTabsDtoArr() as $nav_tab_item_dto) {
                                    ?>
                                    <li role="presentation" <?php echo (strpos($current_url_no_query, $nav_tab_item_dto->getUrl()) !== false ? ' class="active"' : '') ?>>
                                        <a href="<?php echo $nav_tab_item_dto->getUrl(); ?>"<?php echo $nav_tab_item_dto->getTarget() ? 'target="' . $nav_tab_item_dto->getTarget() . '"' : ''; ?>><?php echo $nav_tab_item_dto->getName(); ?></a>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
                        <p></p>
                    <?php
                    }
                    ?>

                    <?php
                    echo $layout_dto->getContentHtml();
                    ?>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 4.4.0
        </div>
        <strong>Copyright &copy; <?php echo '2004 - ' . date('Y'). '. <a href="https://websk.ru" target="_blank">WebSK.RU</a>'?>
    </footer>
</div>
<!-- ./wrapper -->

<!-- AdminLTE App -->
<script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/metisMenu/metisMenu.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/AdminLTE/js/app.min.js'); ?>"></script>

</body>
</html>