<?php
/**
 * @var LayoutDTO $layout_dto
 */

use WebSK\Skif\SkifPath;
use WebSK\Config\ConfWrapper;
use WebSK\Views\LayoutDTO;
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $layout_dto->getTitle(); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="<?php echo SkifPath::wrapUrlPath('/favicon.ico'); ?>" rel="shortcut icon" type="image/x-icon">

    <!-- Bootstrap -->
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css">

    <style>
        html, body {margin: 0;}
        #html {width: 70%; margin: auto; font-size: 12px; line-height: 18px; margin-bottom: 30px;}
        h1 {font-size: 120px; margin: 0; text-align: center; font-weight: 400; margin-bottom: 30px;}
        #footer { margin-top: 30px; text-align: center;}
        @media (max-width: 500px) {
            #html {width: 90%}
            h1 {font-size: 100px;}
        }
    </style>
</head>
<body>

<div id="html">
    <h1><?php echo $layout_dto->getTitle(); ?></h1>
    <div>
        <?php
        echo $layout_dto->getContentHtml();
        ?>
    </div>
    <p>
        <?php
        $site_name = ConfWrapper::value('site_name');
        $site_domain = ConfWrapper::value('site_domain');
        $site_email = ConfWrapper::value('site_email');
        ?>

        Зайдите с <a href="<?php echo $site_domain; ?>">главной страницы</a> или <a href="javascript:history.back()">вернитесь назад</a>.
    </p>

    <p id="footer">&copy;&nbsp; <?php echo $site_name; ?>, <a href="mailto:<?php echo $site_email; ?>" title="написать администратору">администратору</a></p>
</div>
</body>
</html>