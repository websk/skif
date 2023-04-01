<?php
/**
 * @var array $contents_ids_arr
 */

use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Image\ImageManager;
use WebSK\Skif\ContentSanitize;
use WebSK\Slim\Container;

$content_service = ContentServiceProvider::getContentService(Container::self());

foreach ($contents_ids_arr as $content_id) {
    $content_obj = $content_service->getById($content_id);

    $content = ContentSanitize::sanitizeContent($content_obj->getBody())
    ?>
    <div class="list_news">
        <div class="news_data"><?= date('d.m.Y', $content_obj->getUnixTime()) ?></div>
        <div class="news_title">
            <a href="<?php echo $content_obj->getUrl(); ?>"><?php echo $content_obj->getTitle(); ?></a>
        </div>
        <?php
        if ($content_obj->getImage()) {
            ?>
            <div class="news_image"><img
                    src="<?php echo ImageManager::getImgUrlByPreset($content_service->getImagePath($content_obj), '200_auto'); ?>"
                    alt="<?php echo $content_obj->getTitle() ?>" title="<?php echo $content_obj->getTitle() ?>"
                    class="img-responsive"></div>
            <?php
        }
        ?>
        <div><?php echo ContentSanitize::sanitizeContent($content_obj->getAnnotation()) ?></div>
    </div>
<?php
}
?>
