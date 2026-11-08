<?php
/**
 * @var list<int> $contents_ids_arr
 */

use WebSK\Image\ImageManager;
use WebSK\Skif\Content\ContentSanitize;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\TemplateSanitize;
use WebSK\Slim\Container;
use WebSK\Utils\Sanitize;

$content_service = ContentServiceProvider::getContentService(Container::self());

foreach ($contents_ids_arr as $content_id) {
    $content_obj = $content_service->getById($content_id);

    $content = ContentSanitize::sanitizeContent($content_obj->getBody())
    ?>
    <article class="card card-primary card-outline mb-3 content-list-item">
        <div class="card-header d-flex justify-content-between align-items-start gap-3">
            <h2 class="card-title mb-0">
                <a href="<?php echo TemplateSanitize::urlAttribute($content_obj->getUrl()); ?>" class="text-decoration-none"><?php echo Sanitize::sanitizeTagContent($content_obj->getTitle()); ?></a>
            </h2>
            <time class="badge text-bg-light flex-shrink-0" datetime="<?php echo date('Y-m-d', $content_obj->getUnixTime()); ?>"><?php echo date('d.m.Y', $content_obj->getUnixTime()); ?></time>
        </div>
        <div class="card-body">
        <?php
        if ($content_obj->getImage()) {
            ?>
            <div class="mb-3"><img
                    src="<?php echo Sanitize::sanitizeAttrValue(ImageManager::getImgUrlByPreset($content_service->getImagePath($content_obj), '200_auto')); ?>"
                    alt="<?php echo Sanitize::sanitizeAttrValue($content_obj->getTitle()); ?>"
                    class="img-fluid rounded"></div>
            <?php
        }
        ?>
        <div><?php echo ContentSanitize::sanitizeContent($content_obj->getAnnotation()) ?></div>
        </div>
    </article>
<?php
}
?>
