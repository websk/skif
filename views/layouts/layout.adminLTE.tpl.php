<?php
/**
 * @var LayoutDTO $layout_dto
 */

use WebSK\Auth\Auth;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\Users\UsersRoutes;
use WebSK\Auth\Users\UsersUtils;
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
$user_name = $user_obj->getName();

$user_edit_uri = Router::pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_id]);

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

    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/styles/admin.css'); ?>" rel="stylesheet" type="text/css">

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
    <script type="text/javascript" src="/ckeditor/ckeditor.js"></script>

    <style>
        .sidebar-collapse .sidebar-menu .treeview:hover .pull-right-container > .fa {
            display: none;
        }

        .sidebar-collapse .sidebar .user-panel .user-ico {
            width: 30px;
            height: 30px;
        }
    </style>


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
                            <svg viewBox="0 0 50 50" width="25px" height="25px" style="display: block;"
                                 class="user-image">
                                <circle cx="25" cy="25" fill="none" r="24" stroke="#ffffff"
                                        stroke-linecap="round" stroke-miterlimit="10" stroke-width="2"/>
                                <rect fill="none" height="50" width="50"/>
                                <path fill="#ffffff"
                                      d="M29.933,35.528c-0.146-1.612-0.09-2.737-0.09-4.21c0.73-0.383,2.038-2.825,2.259-4.888c0.574-0.047,1.479-0.607,1.744-2.818  c0.143-1.187-0.425-1.855-0.771-2.065c0.934-2.809,2.874-11.499-3.588-12.397c-0.665-1.168-2.368-1.759-4.581-1.759  c-8.854,0.163-9.922,6.686-7.981,14.156c-0.345,0.21-0.913,0.878-0.771,2.065c0.266,2.211,1.17,2.771,1.744,2.818  c0.22,2.062,1.58,4.505,2.312,4.888c0,1.473,0.055,2.598-0.091,4.21c-1.261,3.39-7.737,3.655-11.473,6.924  c3.906,3.933,10.236,6.746,16.916,6.746s14.532-5.274,15.839-6.713C37.688,39.186,31.197,38.93,29.933,35.528z"/>
                            </svg>
                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span class="hidden-xs"><?= $user_name ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header">
                                <svg viewBox="0 0 50 50" width="90px" height="90px"
                                     style="display: block;margin: auto;">
                                    <circle cx="25" cy="25" fill="none" r="24" stroke="#ffffff"
                                            stroke-linecap="round" stroke-miterlimit="10" stroke-width="2"/>
                                    <rect fill="none" height="50" width="50"/>
                                    <path fill="#ffffff"
                                          d="M29.933,35.528c-0.146-1.612-0.09-2.737-0.09-4.21c0.73-0.383,2.038-2.825,2.259-4.888c0.574-0.047,1.479-0.607,1.744-2.818  c0.143-1.187-0.425-1.855-0.771-2.065c0.934-2.809,2.874-11.499-3.588-12.397c-0.665-1.168-2.368-1.759-4.581-1.759  c-8.854,0.163-9.922,6.686-7.981,14.156c-0.345,0.21-0.913,0.878-0.771,2.065c0.266,2.211,1.17,2.771,1.744,2.818  c0.22,2.062,1.58,4.505,2.312,4.888c0,1.473,0.055,2.598-0.091,4.21c-1.261,3.39-7.737,3.655-11.473,6.924  c3.906,3.933,10.236,6.746,16.916,6.746s14.532-5.274,15.839-6.713C37.688,39.186,31.197,38.93,29.933,35.528z"/>
                                </svg>

                                <p><?= $user_name ?></p>
                                <a href="<?php echo $user_edit_uri ?>">Редактировать профиль</a>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="<?php echo Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGOUT, [], ['destination' => Router::pathFor(SkifApp::ROUTE_NAME_ADMIN)]); ?>"
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
                    <svg viewBox="0 0 50 50" width="45px" height="45px" style="display: block;" class="user-ico">
                        <circle cx="25" cy="25" fill="none" r="24" stroke="#b8c7ce" stroke-linecap="round"
                                stroke-miterlimit="10" stroke-width="2"/>
                        <rect fill="none" height="50" width="50"/>
                        <path fill="#b8c7ce"
                              d="M29.933,35.528c-0.146-1.612-0.09-2.737-0.09-4.21c0.73-0.383,2.038-2.825,2.259-4.888c0.574-0.047,1.479-0.607,1.744-2.818  c0.143-1.187-0.425-1.855-0.771-2.065c0.934-2.809,2.874-11.499-3.588-12.397c-0.665-1.168-2.368-1.759-4.581-1.759  c-8.854,0.163-9.922,6.686-7.981,14.156c-0.345,0.21-0.913,0.878-0.771,2.065c0.266,2.211,1.17,2.771,1.744,2.818  c0.22,2.062,1.58,4.505,2.312,4.888c0,1.473,0.055,2.598-0.091,4.21c-1.261,3.39-7.737,3.655-11.473,6.924  c3.906,3.933,10.236,6.746,16.916,6.746s14.532-5.274,15.839-6.713C37.688,39.186,31.197,38.93,29.933,35.528z"/>
                    </svg>
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
            <style>
                .content-header > .breadcrumb {
                    position: relative;
                    margin-top: 5px;
                    top: 0;
                    right: 0;
                    float: none;
                    padding-left: 0;
                    font-size: 16px;
                    display: inline-block;
                    vertical-align: middle;
                    background: transparent !important;
                }

                .content-header > .breadcrumb > li:after {
                    padding: 0 2px 0 4px;
                    content: '/\00a0';
                    color: #b0b0b0;
                }

                .content-header > .breadcrumb > li:last-child:after {
                    content: none;
                }

                .content-header > .breadcrumb > li + li:before {
                    content: none;
                }

                .content-header > .breadcrumb > li > a {
                    color: #3c8dbc;
                }

                .content-header > .breadcrumb > li > .bc-title {
                    display: inline;
                    font-size: 24px;
                    color: #333333;
                }

                .content-header > .toolbar {
                    display: inline-block;
                    vertical-align: middle;
                }
            </style>
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
        <strong>Copyright &copy; 2018.
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Create the tabs -->
        <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
            <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a>
            </li>
            <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
            <!-- Home tab content -->
            <div class="tab-pane active" id="control-sidebar-home-tab">
                <h3 class="control-sidebar-heading">Recent Activity</h3>
                <ul class="control-sidebar-menu">
                    <li>
                        <a href="javascript::;">
                            <i class="menu-icon fa fa-birthday-cake bg-red"></i>

                            <div class="menu-info">
                                <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                                <p>Will be 23 on April 24th</p>
                            </div>
                        </a>
                    </li>
                </ul>
                <!-- /.control-sidebar-menu -->

                <h3 class="control-sidebar-heading">Tasks Progress</h3>
                <ul class="control-sidebar-menu">
                    <li>
                        <a href="javascript::;">
                            <h4 class="control-sidebar-subheading">
                                Custom Template Design
                                <span class="pull-right-container">
                  <span class="label label-danger pull-right">70%</span>
                </span>
                            </h4>

                            <div class="progress progress-xxs">
                                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                            </div>
                        </a>
                    </li>
                </ul>
                <!-- /.control-sidebar-menu -->

            </div>
            <!-- /.tab-pane -->
            <!-- Stats tab content -->
            <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
            <!-- /.tab-pane -->
            <!-- Settings tab content -->
            <div class="tab-pane" id="control-sidebar-settings-tab">
                <form method="post">
                    <h3 class="control-sidebar-heading">General Settings</h3>

                    <div class="form-group">
                        <label class="control-sidebar-subheading">
                            Report panel usage
                            <input type="checkbox" class="pull-right" checked>
                        </label>

                        <p>
                            Some information about this general settings option
                        </p>
                    </div>
                    <!-- /.form-group -->
                </form>
            </div>
            <!-- /.tab-pane -->
        </div>
    </aside>
    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>


</div>
<!-- ./wrapper -->

<!-- AdminLTE App -->
<script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/AdminLTE/js/app.min.js'); ?>"></script>

</body>
</html>