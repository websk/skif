<?php
/**
 * @var LayoutDTO $layout_dto
 */

use WebSK\Auth\Auth;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\Users\UsersRoutes;
use WebSK\Auth\Users\UsersUtils;
use WebSK\Image\ImageManager;
use WebSK\Image\ImagePresets;
use WebSK\Skif\SkifApp;
use WebSK\Skif\SkifPath;
use WebSK\Slim\Router;
use WebSK\Utils\Messages;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;


$user_id = Auth::getCurrentUserId();
$user_obj = UsersUtils::loadUser($user_id);
if (!$user_obj) {
    echo PhpRender::renderLocalTemplate(
        'layout.admin_login.tpl.php'
    );

    return;
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

$user_obj = Auth::getCurrentUserObj();
if (!$user_obj) {
    return '';
}
$user_name = $user_obj->getName();
$user_photo_path = ImageManager::getImgUrlByPreset($user_obj->getPhotoPath(), ImagePresets::IMAGE_PRESET_200_auto);
$user_name = $user_obj->getName();

$user_edit_uri = Router::pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_id]);
$user_logout_url = Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGOUT, [], ['destination' => Router::pathFor(SkifApp::ROUTE_NAME_ADMIN)]);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>СКИФ. <?php echo $layout_dto->getTitle() ?></title>

    <link href="<?php echo SkifPath::wrapSkifUrlPath('/favicon.ico'); ?>" rel="shortcut icon" type="image/x-icon">

    <!-- jQuery -->
    <script src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/jquery/jquery.min.js'); ?>"></script>
    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/jquery-ui/themes/base/jquery-ui.min.css'); ?>" rel="stylesheet" type="text/css">
    <script type="text/javascript"
            src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/jquery-ui/jquery-ui.min.js'); ?>"></script>

    <!-- Bootstrap -->
    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css">
    <script src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/bootstrap/js/bootstrap.min.js'); ?>"></script>

    <!-- MetisMenu CSS -->
    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/metisMenu/metisMenu.min.css'); ?>" rel="stylesheet" type="text/css">

    <!-- Ionicons -->
    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/ionicons/ionicons.min.css'); ?>" rel="stylesheet" type="text/css">

    <!-- AdminLTE -->
    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/AdminLTE/css/AdminLTE.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/AdminLTE/css/skin-blue.min.css'); ?>" rel="stylesheet" type="text/css">

    <!-- Font Awesome -->
    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet" type="text/css">

    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/styles/skif.css'); ?>" rel="stylesheet" type="text/css">

    <!-- Jquery Validate -->
    <script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/jquery-validation/jquery.validate.min.js'); ?>"></script>

    <!-- Fancybox -->
    <script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/fancybox/jquery.fancybox.pack.js"'); ?>></script>
    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/fancybox/jquery.fancybox.css'); ?>" rel="stylesheet" type="text/css">

    <!-- Moment -->
    <script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/moment/moment.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/moment/moment.ru.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'); ?>"></script>

    <!-- Bootstrap datetimepicker -->
    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css'); ?>" rel="stylesheet" type="text/css">

    <!-- CKEditor -->
    <script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/ckeditor/ckeditor.js'); ?>"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/html5shiv/html5shiv.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/respond.js/respond.min.js'); ?>"></script>
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
                <img src="<?php echo SkifPath::wrapSkifAssetsVersion('images/admin/skif_small_logo.png'); ?>" alt="СКИФ"
                     border="0" title="Система управления сайтом СКИФ / websk.ru" class="img-responsive">
            </span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg">
                <img src="<?php echo SkifPath::wrapSkifAssetsVersion('images/admin/skif_small_logo.png'); ?>" alt="СКИФ"
                     border="0" title="Система управления сайтом СКИФ / websk.ru" class="img-responsive">
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
                            <img src="<?php echo $user_photo_path ?>" class="user-image" alt="User Image">
                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span class="hidden-xs"><?= $user_name ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header">
                                <img src="<?php echo $user_photo_path ?>" class="img-circle" alt="User Image">

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
                    <img src="<?php echo $user_photo_path ?>" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p><a href="<?php echo $user_edit_uri ?>"><?= $user_name ?></a></p>
                    <!-- Status -->
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <?php
            ?>

            <!-- Sidebar Menu -->
            <?php
            echo PhpRender::renderLocalTemplate(
                '../admin_menu.tpl.php'
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
<script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/metisMenu/metisMenu.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/AdminLTE/js/app.min.js'); ?>"></script>

</body>
</html>