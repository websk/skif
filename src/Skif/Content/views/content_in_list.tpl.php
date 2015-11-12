<?php
/**
 * @var $content_id
 */

$content_obj = \Skif\Content\Content::factory($content_id);
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
            <div class="col-md-3 news_image"><img src="<?php echo \Skif\Image\ImageManager::getImgUrlByPreset($content_obj->getImagePath(), '120_auto'); ?>" alt="<?php $content_obj->getTitle() ?>" title="<?php echo $content_obj->getTitle() ?>" class="img-responsive"></div>
            <?php
        }
        ?>
        <div class="col-md-<?php echo $col; ?>"><?= \Skif\Content\ContentUtils::filterContent($content_obj->getAnnotation()) ?></div>
    </div>
</div>

