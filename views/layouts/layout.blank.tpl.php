<?php
/**
 * @var $title
 * @var $content
 * @var LayoutDTO $layout_dto
 */

use WebSK\Skif\SkifPath;
use WebSK\Views\LayoutDTO;

if (!isset($layout_dto)) {
    $layout_dto = new LayoutDTO();
    $layout_dto->setTitle($title);
    $layout_dto->setContentHtml($content);
}
?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $layout_dto->getTitle(); ?></title>

    <link href="<?php echo SkifPath::wrapAssetsVersion('/favicon.ico'); ?>" rel="shortcut icon" type="image/x-icon">
</head>
<body>
<?php echo $layout_dto->getContentHtml() ?>
</body>
</html>