<?php
/**
 * @var $content_id
 */

$content_obj = \Skif\Content\Content::factory($content_id);

$content = $content_obj->getBody();
if (!$content) {
    $content = $content_obj->getAnnotation();
}
?>

<div><?= \Skif\Content\ContentUtils::filterContent($content) ?></div>
