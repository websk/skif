<?php
/**
 * @var string $title
 * @var string $content
 * @var array $breadcrumbs_arr
 * @var $editor_nav_arr
 * @var LayoutDTO $layout_dto
 */

use WebSK\Skif\Blocks\PageRegionsUtils;
use WebSK\Utils\Messages;
use WebSK\Skif\SkifPath;
use WebSK\Skif\SiteMenu\SiteMenuRender;
use WebSK\Config\ConfWrapper;
use WebSK\Utils\Sanitize;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Utils\Url;
use WebSK\Views\NavTabItemDTO;
use WebSK\Views\PhpRender;

if (!isset($layout_dto)) {
    $layout_dto = new LayoutDTO();
    $layout_dto->setTitle($title);
    $layout_dto->setContentHtml($content);
    $layout_dto->setKeywords($keywords ?? '');
    $layout_dto->setDescription($description ?? '');

    $nav_tabs_dto_arr = [];
    if (!empty($editor_nav_arr)) {
        foreach ($editor_nav_arr as $editor_nav_url => $editor_nav_title) {
            $nav_tabs_dto_arr[] = new NavTabItemDTO($editor_nav_title, $editor_nav_url);
        }
    }
    $layout_dto->setNavTabsDtoArr($nav_tabs_dto_arr);

    $breadcrumbs_dto_arr = [
        new BreadcrumbItemDTO('Главная', '/')
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
<html lang="ru">
<head xmlns:og="http://ogp.me/ns#">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo Sanitize::sanitizeTagContent($layout_dto->getTitle()); ?></title>

    <link href="<?php echo SkifPath::wrapAssetsVersion('/favicon.ico'); ?>" rel="shortcut icon" type="image/x-icon">

    <!-- jQuery -->
    <script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery/jquery.min.js'); ?>"></script>
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery-ui/themes/base/jquery-ui.min.css'); ?>" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery-ui/jquery-ui.min.js'); ?>"></script>

    <!-- Bootstrap -->
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/admin-lte/css/AdminLTE.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/crud/crud.css'); ?>" rel="stylesheet" type="text/css">

    <link href="<?php echo SkifPath::wrapAssetsVersion('/styles/main.css'); ?>" rel="stylesheet" type="text/css">

    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/font-awesome/css/all.min.css'); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/font-awesome/css/v4-shims.min.css'); ?>" rel="stylesheet" type="text/css">

    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery-validation/jquery.validate.js'); ?>"></script>

    <meta name="keywords" content="<?php echo Sanitize::sanitizeAttrValue($layout_dto->getKeywords()); ?>">
    <meta name="description" content="<?php echo Sanitize::sanitizeAttrValue($layout_dto->getDescription()); ?>">

    <?php
    echo PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('inside_head', 'main');
    ?>
</head>
<body class="bg-body-tertiary">

<a class="skip-link" href="#content">Перейти к содержимому</a>

<div id="html" class="container py-3">
    <header id="header" class="row align-items-center bg-body rounded shadow-sm px-2 py-3 mb-4">
        <div class="col-sm-6">
            <a href="/" aria-label="На главную"><img src="<?php echo SkifPath::wrapAssetsVersion('images/admin/skif_small_logo.png'); ?>"
                             alt="СКИФ" class="img-fluid"></a>
        </div>
        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
            Демо-сайт
        </div>
    </header>

    <main class="row g-4">
            <aside id="sidebar" class="col-12 col-md-3">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-body p-2">
                        <?php
                        echo PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('left_column', 'main');
                        ?>
                    </div>
                </div>
            </aside>
            <section id="content" class="col-12 col-md-9 col-lg-6" tabindex="-1">
                <?php $current_url_no_query = Url::getUriNoQueryString(); ?>
                <div class="app-content-header">
                    <div class="row align-items-center g-2">
                        <?php if ($current_url_no_query != '/') { ?>
                            <div class="col-sm-6">
                                <h1 class="mb-0 fs-4"><?php echo Sanitize::sanitizeTagContent($layout_dto->getTitle()); ?></h1>
                            </div>
                        <?php } ?>
                        <div class="<?php echo $current_url_no_query != '/' ? 'col-sm-6' : 'col-12'; ?>">
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
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-body">
                        <?php echo Messages::renderMessages(); ?>

                        <?php
                        echo PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('above_content', 'main');

                        echo PhpRender::renderLocalTemplate(
                            '../editor_nav.tpl.php',
                            ['nav_tabs_dto_arr' => $layout_dto->getNavTabsDtoArr()]
                        );
                        ?>

                        <?php echo $layout_dto->getContentHtml(); ?>

                        <?php
                        echo PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('under_content', 'main');
                        ?>
                    </div>
                </div>
            </section>
            <aside id="right" class="col-12 col-lg-3">
                <div class="card card-secondary card-outline shadow-sm">
                    <div class="card-body">
                        <?php
                        echo PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('right_column', 'main');
                        ?>
                    </div>
                </div>
            </aside>
    </main>

    <footer id="footer" class="app-footer row mt-4 rounded shadow-sm">
        <div class="col-sm-6">&copy; <?php echo Sanitize::sanitizeTagContent((string) ConfWrapper::value('site_name')); ?>
            , <?php echo date('Y'); ?></div>
        <div class="col-sm-6"><?php echo SiteMenuRender::renderSiteMenu(8); ?></div>
    </footer>
</div>

<script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/admin-lte/js/adminlte.min.js'); ?>"></script>
</body>
</html>
