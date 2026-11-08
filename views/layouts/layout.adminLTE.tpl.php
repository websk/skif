<?php
/**
 * @var LayoutDTO $layout_dto
 */

use WebSK\Auth\Auth;
use WebSK\Auth\AuthRoutes;
use WebSK\Auth\User\UserRoutes;
use WebSK\Auth\User\UserService;
use WebSK\Config\ConfWrapper;
use WebSK\Image\ImageManager;
use WebSK\Image\ImagePresets;
use WebSK\Skif\SkifApp;
use WebSK\Skif\SkifPath;
use WebSK\Slim\Router;
use WebSK\Utils\Messages;
use WebSK\Utils\Sanitize;
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
        new BreadcrumbItemDTO('Главная', Router::urlFor(SkifApp::ROUTE_NAME_ADMIN))
    ];
    if (!empty($breadcrumbs_arr)) {
        foreach ($breadcrumbs_arr as $breadcrumb_title => $breadcrumb_link) {
            $breadcrumbs_dto_arr[] = new BreadcrumbItemDTO($breadcrumb_title, $breadcrumb_link);
        }
    }
    $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_dto_arr);
}

$user_name = $user_obj->getName();
$user_photo_path = ImageManager::getImgUrlByPreset(
    UserService::PHOTO_DIR . DIRECTORY_SEPARATOR . $user_obj->getPhoto(),
    ImagePresets::IMAGE_PRESET_200_auto
);
$user_email = $user_obj->getEmail();
$user_edit_uri = Router::urlFor(UserRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $user_obj->getId()]);
$user_logout_url = Router::urlFor(
    AuthRoutes::ROUTE_NAME_AUTH_LOGOUT,
    [],
    ['destination' => Router::urlFor(SkifApp::ROUTE_NAME_ADMIN)]
);
$content_html = $layout_dto->getContentHtml();
$needs_rich_text_editor = str_contains($content_html, 'data-skif-rich-text-editor');
$needs_glightbox = str_contains($content_html, 'data-glightbox') || str_contains($content_html, 'GLightbox(');
?>
<!doctype html>
<html lang="ru" data-lte-color-mode="off">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>СКИФ. <?php echo Sanitize::sanitizeTagContent($layout_dto->getTitle()); ?></title>
    <link href="<?php echo SkifPath::wrapAssetsVersion('/favicon.ico'); ?>" rel="shortcut icon" type="image/x-icon">

    <script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery/jquery.min.js'); ?>"></script>
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery-ui/themes/base/jquery-ui.min.css'); ?>" rel="stylesheet">
    <script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery-ui/jquery-ui.min.js'); ?>"></script>

    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/admin-lte/css/AdminLTE.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/crud/crud.css'); ?>" rel="stylesheet">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/font-awesome/css/all.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/font-awesome/css/v4-shims.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/styles/skif.css'); ?>" rel="stylesheet">

    <script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery-validation/jquery.validate.js'); ?>"></script>
    <?php if ($needs_glightbox) { ?>
        <script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/glightbox/glightbox.min.js'); ?>"></script>
        <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/glightbox/glightbox.min.css'); ?>" rel="stylesheet">
    <?php } ?>
    <?php if ($needs_rich_text_editor) { ?>
        <script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/jodit/jodit.min.js'); ?>"></script>
        <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/jodit/jodit.min.css'); ?>" rel="stylesheet">
        <script src="<?php echo SkifPath::wrapAssetsVersion('/scripts/rich-text-editor.js'); ?>"></script>
    <?php } ?>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<a class="skip-link" href="#main-content">Перейти к содержимому</a>
<div class="app-wrapper">
    <nav class="app-header navbar navbar-expand bg-body border-bottom shadow-sm">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <button class="nav-link border-0 bg-transparent" data-lte-toggle="sidebar" type="button" aria-label="Открыть меню">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </button>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown user-menu">
                    <button type="button" class="nav-link dropdown-toggle border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if ($user_obj->getPhoto()) { ?>
                            <img src="<?php echo Sanitize::sanitizeAttrValue($user_photo_path); ?>" class="user-image rounded-circle shadow" alt="">
                        <?php } else { ?>
                            <i class="fa fa-user-circle fa-lg me-1" aria-hidden="true"></i>
                        <?php } ?>
                        <span class="d-none d-md-inline"><?php echo Sanitize::sanitizeTagContent($user_name); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                        <li class="user-header text-bg-primary">
                            <?php if ($user_obj->getPhoto()) { ?>
                                <img src="<?php echo Sanitize::sanitizeAttrValue($user_photo_path); ?>" class="rounded-circle shadow" alt="">
                            <?php } else { ?>
                                <i class="fa fa-user-circle fa-5x" aria-hidden="true"></i>
                            <?php } ?>
                            <p><?php echo Sanitize::sanitizeTagContent($user_name); ?><small><?php echo Sanitize::sanitizeTagContent($user_email); ?></small></p>
                        </li>
                        <li class="user-body text-center">
                            <a href="/" target="_blank" rel="noopener">
                                <i class="fa fa-external-link" aria-hidden="true"></i> Перейти на сайт
                            </a>
                        </li>
                        <li class="user-footer">
                            <a href="<?php echo Sanitize::sanitizeAttrValue($user_edit_uri); ?>" class="btn btn-outline-secondary">Мой профиль</a>
                            <a href="<?php echo Sanitize::sanitizeAttrValue($user_logout_url); ?>" class="btn btn-outline-danger float-end">Выход</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <div class="sidebar-brand">
            <a href="<?php echo Router::urlFor(SkifApp::ROUTE_NAME_ADMIN); ?>" class="brand-link"
               aria-label="СКИФ" title="Система управления сайтом СКИФ / websk.ru">
                <img src="<?php echo SkifPath::wrapAssetsVersion('images/admin/skif_small_logo_adminlte.png'); ?>"
                     alt="СКИФ" class="brand-image">
            </a>
        </div>
        <div class="sidebar-search">
            <label for="admin-sidebar-search" class="visually-hidden">Поиск по меню</label>
            <input id="admin-sidebar-search" class="form-control" type="search"
                   placeholder="Поиск по меню…" autocomplete="off"
                   aria-controls="admin-sidebar-menu" data-lte-toggle="sidebar-search">
            <div class="small text-body-secondary mt-2 px-2" data-lte-search-empty hidden>
                Ничего не найдено
            </div>
        </div>
        <div class="sidebar-wrapper">
            <nav class="mt-2" aria-label="Административное меню">
                <?php
                echo PhpRender::renderLocalTemplate(
                    '../admin_menu.tpl.php',
                    ['admin_menu_arr' => ConfWrapper::value('skif.menu', [])]
                );
                ?>
            </nav>
        </div>
    </aside>

    <main class="app-main" id="main-content" tabindex="-1">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-sm-6"><h1 class="mb-0 fs-3"><?php echo Sanitize::sanitizeTagContent($layout_dto->getTitle()); ?></h1></div>
                    <div class="col-sm-6">
                        <nav class="d-flex justify-content-sm-end" aria-label="Хлебные крошки">
                            <?php
                            echo PhpRender::renderLocalTemplate(
                                '../breadcrumbs.tpl.php',
                                ['breadcrumbs_dto_arr' => $layout_dto->getBreadcrumbsDtoArr()]
                            );
                            ?>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-body">
                        <?php echo Messages::renderMessages(); ?>
                        <?php if ($layout_dto->getNavTabsDtoArr()) { ?>
                            <?php $current_url_no_query = Url::getUriNoQueryString(); ?>
                            <ul class="nav nav-tabs mb-3">
                                <?php foreach ($layout_dto->getNavTabsDtoArr() as $nav_tab_item_dto) { ?>
                                    <?php $is_active = str_contains($current_url_no_query, $nav_tab_item_dto->getUrl()); ?>
                                    <li class="nav-item">
                                        <a class="nav-link<?php echo $is_active ? ' active' : ''; ?>"
                                           href="<?php echo Sanitize::sanitizeAttrValue($nav_tab_item_dto->getUrl()); ?>"
                                           <?php echo $nav_tab_item_dto->getTarget() ? 'target="' . Sanitize::sanitizeAttrValue($nav_tab_item_dto->getTarget()) . '" rel="noopener"' : ''; ?>
                                           <?php echo $is_active ? 'aria-current="page"' : ''; ?>><?php echo Sanitize::sanitizeTagContent($nav_tab_item_dto->getName()); ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                        <?php echo $content_html; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="app-footer">
        <div class="float-end d-none d-sm-inline"><b>AdminLTE</b> 4.10.0</div>
        <strong>Copyright &copy; <?php echo '2004 - ' . date('Y'); ?>.
            <a href="https://websk.ru" target="_blank" rel="noopener" class="text-decoration-none">WebSK.RU</a>
        </strong>
    </footer>
</div>

<script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/admin-lte/js/adminlte.min.js'); ?>"></script>
<script src="<?php echo SkifPath::wrapAssetsVersion('/scripts/admin-ui.js'); ?>"></script>
</body>
</html>
