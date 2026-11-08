<?php
/** @var LayoutDTO $layout_dto */
use WebSK\Config\ConfWrapper;
use WebSK\Skif\SkifPath;
use WebSK\Utils\Sanitize;
use WebSK\Views\LayoutDTO;

$site_name = (string) ConfWrapper::value('site_name');
$site_domain = (string) ConfWrapper::value('site_domain');
$site_email = (string) ConfWrapper::value('site_email');
?>
<!doctype html>
<html lang="ru" data-lte-color-mode="off">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo Sanitize::sanitizeTagContent($layout_dto->getTitle()); ?></title>
    <link href="<?php echo SkifPath::wrapAssetsVersion('/favicon.ico'); ?>" rel="shortcut icon" type="image/x-icon">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/admin-lte/css/AdminLTE.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/libraries/font-awesome/css/all.min.css'); ?>" rel="stylesheet">
    <link href="<?php echo SkifPath::wrapAssetsVersion('/styles/skif.css'); ?>" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
<main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="card card-danger card-outline shadow-sm error-box w-100">
        <div class="card-header">
            <h1 class="card-title mb-0"><?php echo Sanitize::sanitizeTagContent($layout_dto->getTitle()); ?></h1>
        </div>
        <div class="card-body">
            <?php echo $layout_dto->getContentHtml(); ?>
            <p class="mb-0">
                Перейдите на <a href="<?php echo Sanitize::sanitizeAttrValue($site_domain); ?>">главную страницу</a>
                или воспользуйтесь кнопкой браузера «Назад».
            </p>
        </div>
        <div class="card-footer text-body-secondary text-center">
            &copy; <?php echo Sanitize::sanitizeTagContent($site_name); ?>,
            <a href="mailto:<?php echo Sanitize::sanitizeAttrValue($site_email); ?>" title="Написать администратору"><?php echo Sanitize::sanitizeTagContent($site_email); ?></a>
        </div>
    </div>
</main>
</body>
</html>
