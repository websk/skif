<?php
/**
 * @var $contents_ids_arr
 */

use WebSK\Skif\Content\Content;
use WebSK\Skif\Content\ContentUtils;
use WebSK\Skif\Image\ImageManager;

foreach ($contents_ids_arr as $content_id) {
    $content_obj = Content::factory($content_id);

    $content = ContentUtils::filterContent($content_obj->getBody())
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
                    src="<?php echo ImageManager::getImgUrlByPreset($content_obj->getImagePath(), '200_auto'); ?>"
                    alt="<?php $content_obj->getTitle() ?>" title="<?php echo $content_obj->getTitle() ?>"
                    class="img-responsive"></div>
            <?php
        }
        ?>
        <div><?php echo ContentUtils::filterContent($content_obj->getAnnotation()) ?></div>
    </div>
<?php
}
?>
