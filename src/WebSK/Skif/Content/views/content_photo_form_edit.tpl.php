<?php
/**
 * @var string $content_type
 * @var int $content_id
 * @var ContentService $content_service
 */

use WebSK\Skif\Content\ContentService;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentPhotoListHandler;
use WebSK\Skif\Content\RequestHandlers\ContentPhotoCreateHandler;
use WebSK\Skif\SkifPath;
use WebSK\Skif\Router;

$content_obj = $content_service->getById($content_id);

?>
<div id="content_photos_list" data-glightbox-container></div>

<h4>Добавить фото</h4>

<label class="btn btn-success" for="upload_image">
    <i class="fa fa-plus"></i>
    <span>Выберите файлы...</span>
    <input id="upload_image" class="visually-hidden" type="file" name="upload_image[]" multiple
           accept="image/gif,image/jpeg,image/png,.gif,.jpeg,.jpg,.png">
</label>
<br>
<br>

<div id="files" aria-live="polite"></div>

<script src="<?php echo SkifPath::wrapAssetsVersion('/scripts/content-photo-upload.js') ?>"></script>

<script>
    let contentPhotoLightbox;

    function refreshContentPhotoLightbox() {
        if (contentPhotoLightbox) {
            contentPhotoLightbox.reload();
            return;
        }

        contentPhotoLightbox = GLightbox({
            selector: '.content-photo-lightbox'
        });
    }

    function ajaxDeleteImage(content_photo_id) {
        $.ajax({
            type: "POST",
            url: "/admin/content_photo/" + content_photo_id + "/delete",
            success: function () {
                ajaxUpdateImageList();
            }
        });
    }

    function ajaxSetDefaultImage(content_photo_id) {
        $.ajax({
            type: "POST",
            url: "/admin/content_photo/" + content_photo_id + "/set_default",
            success: function () {
                ajaxUpdateImageList();
            }
        });
    }

    function ajaxUpdateImageList() {
        $.ajax({
            url: '<?php echo Router::urlFor(AdminContentPhotoListHandler::class, ['content_type' => $content_type, 'content_id' => $content_id]); ?>',
            success: function (html) {
                $('#content_photos_list').html(html);
                refreshContentPhotoLightbox();
            }
        });
    }

    $(function () {
        ajaxUpdateImageList();

        SkifContentPhotoUpload.init({
            inputSelector: '#upload_image',
            outputSelector: '#files',
            uploadUrl: <?php echo json_encode(Router::urlFor(ContentPhotoCreateHandler::class, ['content_type' => $content_type, 'content_id' => $content_id]), JSON_THROW_ON_ERROR); ?>,
            targetFolder: 'content',
            maxFileSize: 20000000,
            onUploaded: ajaxUpdateImageList
        });
    });
</script>
