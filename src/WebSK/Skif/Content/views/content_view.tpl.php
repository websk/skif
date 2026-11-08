<?php
/**
 * @var int $content_id
 */

use WebSK\Image\ImageManager;
use WebSK\Skif\Content\ContentSanitize;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Slim\Container;
use WebSK\Utils\Sanitize;

$content_service = ContentServiceProvider::getContentService(Container::self());

$content_obj = $content_service->getById($content_id);

$content = $content_obj->getBody();
if (!$content) {
    $content = $content_obj->getAnnotation();
}

if (!$content) {
    return;
}

if ($content_obj->getImage()) {
    ?>
    <div class="mb-3">
        <img src="<?php echo Sanitize::sanitizeAttrValue(ImageManager::getImgUrlByPreset($content_service->getImagePath($content_obj), '400_auto')); ?>"
            alt="<?php echo Sanitize::sanitizeAttrValue($content_obj->getTitle()); ?>" class="img-fluid rounded shadow-sm">
    </div>
<?php
}
?>

<div class="content-body"><?php echo ContentSanitize::sanitizeContent($content) ?></div>
