<?php
/**
 * @var int $content_id
 * @var ContentPhotoService $content_photo_service
 */

use WebSK\Image\ImageManager;
use WebSK\Skif\Content\ContentPhotoService;

$content_photo_ids_arr = $content_photo_service->getIdsArrByContentId($content_id);

?>
<?php

foreach ($content_photo_ids_arr as $content_photo_id) {
    $content_photo_obj = $content_photo_service->getById($content_photo_id);

    $image_default_add_class = '';
    if ($content_photo_obj->isDefault()) {
        $image_default_add_class = ' text-danger';
    }
    ?>
    <div style="margin-bottom: 10px">
        <a data-fancybox="gallery"
           href="<?php echo ImageManager::getImgUrlByFileName($content_photo_obj->getPhotoPath()) ?>"
           class="grouped_elements">
            <img src="<?php echo ImageManager::getImgUrlByPreset($content_photo_obj->getPhotoPath(),
                '160_auto') ?>" class="img-responsive img-thumbnail">
        </a><br>
        <a class="image_delete" data-content-photo-id="<?php echo $content_photo_id; ?>" title="Удалить фотографию">Удалить</a>
        / <a class="image_default<?php echo $image_default_add_class; ?>"
             data-content-photo-id="<?php echo $content_photo_id; ?>"
             title="Использовать фотографию по-умолчанию">По умолчанию</a>
    </div>
    <?php
}
?>
<script>
    $("a.image_delete").click(function () {
        ajaxDeleteImage($(this).data('content-photo-id'));
    });
    $("a.image_default").click(function () {
        ajaxSetDefaultImage($(this).data('content-photo-id'));
    });
</script>