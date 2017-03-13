<?php
/**
 * @var $title
 * @var $breadcrumbs
 * @var $editor_nav_arr
 * @var $content
 */

\Skif\Http::cacheHeaders();
?>
<!DOCTYPE html>
<html lang="ru">
<head xmlns:og="http://ogp.me/ns#">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $title ?></title>

    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon">

    <script type="text/javascript" src="/vendor/bower/jquery/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="/vendor/bower/jquery-ui/themes/base/jquery-ui.min.css">
    <script type="text/javascript" src="/vendor/bower/jquery-ui/jquery-ui.min.js"></script>

    <link type="text/css" rel="stylesheet" media="all" href="/vendor/bower/bootstrap/dist/css/bootstrap.min.css"/>
    <script type="text/javascript" src="/vendor/bower/bootstrap/dist/js/bootstrap.min.js"></script>

    <link type="text/css" rel="stylesheet" media="all" href="/vendor/websk/skif/assets/styles/main.css"/>

    <script type="text/javascript" src="/vendor/bower/jquery-validation/dist/jquery.validate.min.js"></script>

    <script type="text/javascript" src="/vendor/bower/fancybox/source/jquery.fancybox.pack.js"></script>
    <link rel="stylesheet" type="text/css" href="/vendor/bower/fancybox/source/jquery.fancybox.css" media="screen"/>

    <script type="text/javascript" src="/vendor/bower/moment/min/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="/vendor/bower/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <link type="text/css" rel="stylesheet" media="all" href="/vendor/bower/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css"/>

    <?php
    echo \Skif\Blocks\PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('inside_head', 'main');
    ?>
</head>
<body>

<div id="html">
    <div id="header" class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <a href="/"><img src="/skif/images/admin/skif.gif" border="0" alt="" title="" class="img-responsive"></a>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" align="right">
            <div class="row icon_row" align="right">
            </div>
        </div>
    </div>

    <div>
        <div class="row">
            <div id="sidebar" class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                <?php
                echo \Skif\Blocks\PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('left_column', 'main');
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

                $current_url_no_query = \Skif\UrlManager::getUriNoQueryString();
                if ($current_url_no_query != '/') {

                    ?>
                    <h1><?= $title ?></h1>
                    <hr class="hidden-xs hidden-sm">
                <?
                }
                ?>

                <?php
                echo \Skif\Messages::renderMessages();
                ?>

                <?php
                echo \Skif\Blocks\PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('above_content', 'main');
                ?>

                <?php
                if (isset($editor_nav_arr)) {
                    echo \Skif\PhpTemplate::renderTemplate('views/editor_nav.tpl.php', array('editor_nav_arr' => $editor_nav_arr));
                }
                ?>

                <?php echo $content; ?>

                <?php
                echo \Skif\Blocks\PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('under_content', 'main');
                ?>
            </div>
            <div id="right" class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
                <?php
                echo \Skif\Blocks\PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName('right_column', 'main');
                ?>
            </div>
        </div>
    </div>

    <div id="footer" class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">&copy; <?php echo \Skif\Conf\ConfWrapper::value('site_name'); ?>, <?php echo date('Y'); ?></div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12"><?php echo \Skif\SiteMenu\SiteMenuRender::renderSiteMenu(8); ?></div>
    </div>
</div>

</body>
</html>