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

    <title><?= $layout_dto->getTitle() ?></title>

    <link href="<?php echo SkifPath::wrapAssetsVersion('/favicon.ico'); ?>" rel="shortcut icon" type="image/x-icon">

    <!-- jQuery -->
    <script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery/jquery.min.js'); ?>"></script>
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery-ui/themes/base/jquery-ui.min.css'); ?>" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery-ui/jquery-ui.min.js'); ?>"></script>

    <!-- Bootstrap -->
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css">
    <script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap/js/bootstrap.min.js'); ?>"></script>

    <link href="<?php echo SkifPath::wrapAssetsVersion('/styles/main.css'); ?>" rel="stylesheet" type="text/css">

    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet" type="text/css">

    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/jquery-validation/jquery.validate.js'); ?>"></script>

    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/fancybox/jquery.fancybox.min.js"'); ?>"></script>
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/fancybox/jquery.fancybox.min.css'); ?>" rel="stylesheet" type="text/css">

    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/moment/moment.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/moment/moment.ru.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js'); ?>"></script>
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css'); ?>" rel="stylesheet" type="text/css">

    <meta name="keywords" content="<?php echo $layout_dto->getKeywords() ?>">
    <meta name="description" content="<?php echo $layout_dto->getDescription() ?>">

    <?php
    echo PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('inside_head', 'main');
    ?>
</head>
<body>

<div id="html">
    <div id="header" class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <a href="/"><img src="<?php echo SkifPath::wrapAssetsVersion('images/admin/skif_small_logo.png'); ?>" border="0" alt="" title=""
                             class="img-responsive"></a>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" align="right">
            <div class="row icon_row" align="right">
                Демо-сайт
            </div>
        </div>
    </div>

    <div>
        <div class="row">
            <div id="sidebar" class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                <?php
                echo PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('left_column', 'main');
                ?>
            </div>
            <div id="content" class="col-lg-8 col-md-6 col-sm-6 col-xs-12">
                <?php
                echo PhpRender::renderLocalTemplate(
                    '../breadcrumbs.tpl.php',
                    ['breadcrumbs_dto_arr' => $layout_dto->getBreadcrumbsDtoArr()]
                );

                $current_url_no_query = Url::getUriNoQueryString();

                if ($current_url_no_query != '/') {
                    ?>
                    <h1><?= $layout_dto->getTitle() ?></h1>
                    <hr class="hidden-xs hidden-sm">
                    <?php
                }
                ?>

                <?php
                echo Messages::renderMessages();
                ?>

                <?php
                echo PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('above_content', 'main');
                ?>

                <?php
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
            <div id="right" class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
                <?php
                echo PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('right_column', 'main');
                ?>
            </div>
        </div>
    </div>

    <div id="footer" class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">&copy; <?php echo ConfWrapper::value('site_name'); ?>
            , <?php echo date('Y'); ?></div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"><?php echo SiteMenuRender::renderSiteMenu(8); ?></div>
    </div>
</div>

</body>
</html>