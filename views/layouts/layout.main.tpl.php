<?php
/**
 * @var $title
 * @var $breadcrumbs
 * @var $editor_nav_arr
 * @var $content
 */

use WebSK\Utils\Http;
use WebSK\Utils\Url;

Http::cacheHeaders();
?>
<!DOCTYPE html>
<html lang="ru">
<head xmlns:og="http://ogp.me/ns#">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $title ?></title>

    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon">

    <script type="text/javascript" src="/assets/libraries/jquery/jquery.min.js"></script>
    <link rel="stylesheet" href="/assets/libraries/jquery-ui/themes/base/jquery-ui.min.css">
    <script type="text/javascript" src="/assets/libraries/jquery-ui/jquery-ui.min.js"></script>

    <link type="text/css" rel="stylesheet" media="all" href="/assets/libraries/bootstrap/css/bootstrap.min.css"/>
    <script type="text/javascript" src="/assets/libraries/bootstrap/js/bootstrap.min.js"></script>

    <link type="text/css" rel="stylesheet" media="all" href="/assets/styles/main.css"/>

    <script type="text/javascript" src="/assets/libraries/jquery-validation/jquery.validate.min.js"></script>

    <script type="text/javascript" src="/assets/libraries/fancybox/jquery.fancybox.pack.js"></script>
    <link rel="stylesheet" type="text/css" href="/assets/libraries/fancybox/jquery.fancybox.css" media="screen"/>

    <script type="text/javascript" src="/assets/libraries/moment/moment.min.js"></script>
    <script type="text/javascript" src="/assets/libraries/moment/moment.ru.min.js"></script>
    <script type="text/javascript" src="/assets/libraries/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <link type="text/css" rel="stylesheet" media="all" href="/assets/libraries/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>

    <?php
    echo \WebSK\Skif\Blocks\PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('inside_head', 'main');
    ?>
</head>
<body>

<div id="html">
    <div id="header" class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <a href="/"><img src="/assets/images/admin/skif_small_logo.png" border="0" alt="" title="" class="img-responsive"></a>
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
                echo \WebSK\Skif\Blocks\PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('left_column', 'main');
                ?>
            </div>
            <div id="content" class="col-lg-8 col-md-6 col-sm-6 col-xs-12">
                <?php
                if (!isset($breadcrumbs_arr)) {
                    $breadcrumbs_arr = array();
                }

                $breadcrumbs_arr = array_merge(
                    array('Главная' => '/'),
                    $breadcrumbs_arr
                );

                echo \Skif\PhpTemplate::renderTemplate('views/breadcrumbs.tpl.php', array('breadcrumbs_arr' => $breadcrumbs_arr));

                $current_url_no_query = Url::getUriNoQueryString();
                if ($current_url_no_query != '/') {

                    ?>
                    <h1><?= $title ?></h1>
                    <hr class="hidden-xs hidden-sm">
                <?
                }
                ?>

                <?php
                echo \Websk\Skif\Messages::renderMessages();
                ?>

                <?php
                echo \WebSK\Skif\Blocks\PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('above_content', 'main');
                ?>

                <?php
                if (isset($editor_nav_arr)) {
                    echo \Skif\PhpTemplate::renderTemplate('views/editor_nav.tpl.php', array('editor_nav_arr' => $editor_nav_arr));
                }
                ?>

                <?php echo $content; ?>

                <?php
                echo \WebSK\Skif\Blocks\PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('under_content', 'main');
                ?>
            </div>
            <div id="right" class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
                <?php
                echo \WebSK\Skif\Blocks\PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('right_column', 'main');
                ?>
            </div>
        </div>
    </div>

    <div id="footer" class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">&copy; <?php echo \WebSK\Slim\ConfWrapper::value('site_name'); ?>, <?php echo date('Y'); ?></div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"><?php echo \WebSK\Skif\SiteMenu\SiteMenuRender::renderSiteMenu(8); ?></div>
    </div>
</div>

</body>
</html>