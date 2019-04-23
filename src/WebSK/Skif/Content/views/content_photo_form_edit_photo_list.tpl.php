<?php
/**
 * @var $tour_id
 */

if ($tour_id == 'new') {
    return '';
}

$tour_obj = \Tour\Tour::factory($tour_id);

$tour_photo_ids_arr = $tour_obj->getTourPhotoIdsArr();

foreach ($tour_photo_ids_arr as $tour_photo_id) {
    $tour_photo_obj = \Tour\TourPhoto::factory($tour_photo_id);

    $image_default_add_class = '';
    if ($tour_photo_obj->isDefault()) {
        $image_default_add_class = ' uk-badge';
    }
    ?>
    <div class="form-group">
        <a rel="gallery" href="<?php echo \Skif\Image\ImageManager::getImgUrlByFileName($tour_photo_obj->getPhotoPath()) ?>" class="grouped_elements">
            <img src="<?php echo  \Skif\Image\ImageManager::getImgUrlByPreset($tour_photo_obj->getPhotoPath(), '160_auto') ?>" class="img-responsive img-thumbnail" border="0">
        </a>
        <a class="image_delete" data-tour-photo-id="<?php echo $tour_photo_id; ?>" title="Удалить фотографию">Удалить</a>
        / <a class="image_default<?php echo $image_default_add_class; ?>" data-tour-photo-id="<?php echo $tour_photo_id; ?>" title="Использовать фотографию по-умолчанию">По умолчанию</a>
    </div>
    <?php
}
?>
<script>
    $("a.image_delete").click(function () {
        ajaxDeleteImage($(this).data('tour-photo-id'));
    });
    $("a.image_default").click(function () {
        ajaxSetDefaultImage($(this).data('tour-photo-id'));
    });
</script>