<?php
/**
 * @var $content_id
 */

use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\Content\ContentUtils;
use WebSK\Image\ImageManager;
use WebSK\Slim\Container;

$content_service = ContentServiceProvider::getContentService(Container::self());

$content_obj = $content_service->getById($content_id);

?>

<div class="list_news">
    <div class="news_data"><?= date('d.m.Y', $content_obj->getUnixTime()) ?></div>
    <div class="news_title">
        <a href="<?php echo $content_obj->getUrl(); ?>"><?php echo $content_obj->getTitle(); ?></a>
    </div>
    <div class="row">
        <?php
        $col = 12;
        if ($content_obj->getImage()) {
            $col = 9;
            ?>
            <div class="col-md-3 news_image"><img src="<?php echo ImageManager::getImgUrlByPreset($content_service->getImagePath($content_obj), '120_auto'); ?>" alt="<?php $content_obj->getTitle() ?>" title="<?php echo $content_obj->getTitle() ?>" class="img-responsive"></div>
            <?php
        }
        ?>
        <div class="col-md-<?php echo $col; ?>"><?php echo ContentUtils::filterContent($content_obj->getAnnotation()) ?></div>
    </div>
</div>

