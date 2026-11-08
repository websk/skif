<?php
/**
 * @var int $content_id
 * @var ContentPhotoService $content_photo_service
 */

use WebSK\Image\ImageManager;
use WebSK\Skif\Content\ContentPhotoService;
use WebSK\Utils\Sanitize;

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
    <div class="mb-3">
        <a data-gallery="content-photos" data-glightbox
           href="<?php echo Sanitize::sanitizeAttrValue(ImageManager::getImgUrlByFileName($content_photo_obj->getPhotoPath())); ?>"
           class="content-photo-lightbox">
            <img src="<?php echo Sanitize::sanitizeAttrValue(ImageManager::getImgUrlByPreset($content_photo_obj->getPhotoPath(),
                '160_auto')); ?>" alt="" class="img-fluid img-thumbnail">
        </a><br>
        <button type="button" class="btn btn-link image_delete p-0" data-content-photo-id="<?php echo $content_photo_id; ?>" title="Удалить фотографию">Удалить</button>
        / <button type="button" class="btn btn-link image_default p-0<?php echo $image_default_add_class; ?>"
             data-content-photo-id="<?php echo $content_photo_id; ?>"
             title="Использовать фотографию по умолчанию">По умолчанию</button>
    </div>
    <?php
}
?>
<script>
    $(".image_delete").on('click', function () {
        ajaxDeleteImage($(this).data('content-photo-id'));
    });
    $(".image_default").on('click', function () {
        ajaxSetDefaultImage($(this).data('content-photo-id'));
    });
</script>
