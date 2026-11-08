<?php
/**
 * @var string $title
 * @var string $content
 * @var LayoutDTO $layout_dto
 */
use WebSK\Skif\SkifPath;
use WebSK\Utils\Sanitize;
use WebSK\Views\LayoutDTO;

if (!isset($layout_dto)) {
    $layout_dto = new LayoutDTO();
    $layout_dto->setTitle($title);
    $layout_dto->setContentHtml($content);
}
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
    <link href="<?php echo SkifPath::wrapAssetsVersion('/styles/skif.css'); ?>" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
<main class="container py-4">
    <?php echo $layout_dto->getContentHtml(); ?>
</main>
<script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?php echo SkifPath::wrapAssetsVersion('/libraries/admin-lte/js/adminlte.min.js'); ?>"></script>
</body>
</html>
