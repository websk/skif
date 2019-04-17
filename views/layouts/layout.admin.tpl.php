<?php
/**
 * @var string $title
 * @var string $content
 * @var array $breadcrumbs_arr
 * @var LayoutDTO $layout_dto
 */

use WebSK\Auth\AuthRoutes;
use WebSK\Skif\SkifApp;
use WebSK\Auth\Users\UsersRoutes;
use WebSK\Config\ConfWrapper;
use WebSK\Slim\Router;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Utils\Messages;
use WebSK\Skif\SkifPath;
use WebSK\Auth\Auth;
use WebSK\Auth\Users\UsersUtils;
use WebSK\Utils\Url;
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

if (!isset($layout_dto)) {
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>СКИФ - Система управления сайтом</title>

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

    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/sb-admin-2/css/sb-admin-2.min.css'); ?>" rel="stylesheet" type="text/css">

    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet" type="text/css">

    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/styles/skif.css'); ?>" rel="stylesheet" type="text/css">

    <script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/jquery-validation/jquery.validate.min.js'); ?>"></script>

    <script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/fancybox/jquery.fancybox.pack.js"'); ?>></script>
    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/fancybox/jquery.fancybox.css'); ?>" rel="stylesheet" type="text/css">

    <script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/moment/moment.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/moment/moment.ru.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'); ?>"></script>
    <link href="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css'); ?>" rel="stylesheet" type="text/css">

    <script type="text/javascript" src="/ckeditor/ckeditor.js"></script>
</head>

<body>

<div id="wrapper">

    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo Router::pathFor(SkifApp::ROUTE_NAME_ADMIN); ?>">
                <img src="<?php echo SkifPath::wrapSkifAssetsVersion('images/admin/skif_small_logo.png'); ?>" alt="СКИФ"
                     border="0" height="39" title="Система управления сайтом СКИФ / websk.ru">
            </a>
        </div>

        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a href="/" target="_blank">
                    <i class="fa fa-external-link fa-fw"></i>
                </a>
            </li>

            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li></li>
                    <li><a href="<?php echo Router::pathFor(UsersRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => $user_id]); ?>"><i
                                    class="fa fa-user fa-fw"></i> <?php echo $user_name; ?></a>
                    </li>
                    <li class="divider"></li>
                    <li><a href="<?php echo Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGOUT, [], ['destination' => Router::pathFor(SkifApp::ROUTE_NAME_ADMIN)]); ?>"><i class="fa fa-sign-out fa-fw"></i> Выход</a>
                    </li>
                </ul>
            </li>
        </ul>

        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <?php
                    echo PhpRender::renderLocalTemplate(
                        '../admin_menu.tpl.php'
                    );
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div>
                        <?php
                        echo PhpRender::renderLocalTemplate(
                            '../breadcrumbs.tpl.php',
                            ['breadcrumbs_dto_arr' => $layout_dto->getBreadcrumbsDtoArr()]
                        );

                        if ($layout_dto->getTitle()) {
                            echo '<h1 class="page-header">' . $layout_dto->getTitle() . '</h1>';
                        }

                        echo Messages::renderMessages();
                        ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div><?php echo $layout_dto->getContentHtml(); ?></div>
                </div>
            </div>

        </div>
    </div>

</div>

<script src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/metisMenu/metisMenu.min.js'); ?>"></script>
<script src="<?php echo SkifPath::wrapSkifAssetsVersion('/libraries/sb-admin-2/js/sb-admin-2.min.js'); ?>"></script>

</body>
</html>
