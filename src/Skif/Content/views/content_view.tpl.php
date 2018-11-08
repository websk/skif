<?php
/**
 * @var $content_id
 */

$content_obj = \Skif\Content\Content::factory($content_id);

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
        <img src="<?php echo \WebSK\Skif\Image\ImageManager::getImgUrlByPreset($content_obj->getImagePath(), '400_auto'); ?>"
            alt="<?php echo $content_obj->getTitle(); ?>" title="<?php echo $content_obj->getTitle(); ?>" class="img-responsive">
    </p>
<?php
}
?>

<div><?= \Skif\Content\ContentUtils::filterContent($content) ?></div>
