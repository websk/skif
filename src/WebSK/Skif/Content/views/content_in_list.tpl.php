<?php
/**
 * @var int $content_id
 */

use WebSK\Image\ImageManager;
use WebSK\Skif\Content\ContentSanitize;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\TemplateSanitize;
use WebSK\Slim\Container;
use WebSK\Utils\Sanitize;

$content_service = ContentServiceProvider::getContentService(Container::self());

$content_obj = $content_service->getById($content_id);

?>

<article class="card card-primary card-outline mb-3 content-list-item">
    <div class="card-header d-flex justify-content-between align-items-start gap-3">
        <h2 class="card-title mb-0">
            <a href="<?php echo TemplateSanitize::urlAttribute($content_obj->getUrl()); ?>" class="text-decoration-none"><?php echo Sanitize::sanitizeTagContent($content_obj->getTitle()); ?></a>
        </h2>
        <time class="badge text-bg-light flex-shrink-0" datetime="<?php echo date('Y-m-d', $content_obj->getUnixTime()); ?>"><?php echo date('d.m.Y', $content_obj->getUnixTime()); ?></time>
    </div>
    <div class="card-body">
    <div class="row g-3 align-items-start">
        <?php
        $col = 12;
        if ($content_obj->getImage()) {
            $col = 9;
            ?>
            <div class="col-md-3"><img src="<?php echo Sanitize::sanitizeAttrValue(ImageManager::getImgUrlByPreset($content_service->getImagePath($content_obj), '120_auto')); ?>" alt="<?php echo Sanitize::sanitizeAttrValue($content_obj->getTitle()); ?>" class="img-fluid rounded"></div>
            <?php
        }
        ?>
        <div class="col-md-<?php echo $col; ?>"><?php echo ContentSanitize::sanitizeContent($content_obj->getAnnotation()) ?></div>
    </div>
    </div>
</article>

