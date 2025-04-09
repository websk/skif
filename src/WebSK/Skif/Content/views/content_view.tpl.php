<?php
/**
 * @var int $content_id
 */

use WebSK\Image\ImageManager;
use WebSK\Skif\Content\ContentSanitize;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Slim\Container;

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
    <p>
        <img src="<?php echo ImageManager::getImgUrlByPreset($content_service->getImagePath($content_obj), '400_auto'); ?>"
            alt="<?php echo $content_obj->getTitle(); ?>" title="<?php echo $content_obj->getTitle(); ?>" class="img-responsive">
    </p>
<?php
}
?>

<div><?php echo ContentSanitize::sanitizeContent($content) ?></div>
