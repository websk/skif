<?php
/**
 * @var int $content_id
 * @var string $content_type
 * @var ContentService $content_service
 * @var ContentTypeService $content_type_service
 * @var RubricService $rubric_service
 * @var TemplateService $template_service
 */

use WebSK\Skif\Content\Content;
use WebSK\Skif\Content\ContentService;
use WebSK\Skif\RichTextEditor\RichTextEditor;
use WebSK\Image\ImageManager;
use WebSK\Logger\LoggerRender;
use WebSK\Skif\Content\ContentTypeService;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentDeleteImageAction;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentSaveHandler;
use WebSK\Skif\Content\RubricService;
use WebSK\Skif\Content\TemplateService;
use WebSK\Skif\Router;
use WebSK\Utils\Sanitize;
use WebSK\Views\PhpRender;

$content_type_obj = $content_type_service->getByType($content_type);

$content_obj = $content_service->getById($content_id);

$rubric_ids_arr = $rubric_service->getIdsArrByContentTypeId($content_type_obj->getId());
$published_at = $content_obj->getPublishedAt();
$unpublished_at = $content_obj->getUnpublishedAt();
?>
<script>
    $().ready(function () {
        $("#content_edit_form").validate({
            ignore: ":hidden",
            rules: {
                title: "required"
            },
            messages: {
                title: "Это поле обязательно для заполнения"
            }
        });
    })
</script>

<form class="" id="content_edit_form"
      action="<?php echo Router::urlFor(AdminContentSaveHandler::class, ['content_type' => $content_type, 'content_id' => $content_id]); ?>" enctype="multipart/form-data"
      method="post">
    <div role="tabpanel">

        <ul id="contentTab" class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" id="content-tab" type="button" role="tab" data-bs-toggle="tab" data-bs-target="#content" aria-controls="content" aria-selected="true">Контент</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="publish-tab" type="button" role="tab" data-bs-toggle="tab" data-bs-target="#publish" aria-controls="publish" aria-selected="false">Настройки публикации</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="rubrics-tab" type="button" role="tab" data-bs-toggle="tab" data-bs-target="#rubrics" aria-controls="rubrics" aria-selected="false">Рубрики</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="seo-tab" type="button" role="tab" data-bs-toggle="tab" data-bs-target="#seo" aria-controls="seo" aria-selected="false">SEO</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="photo-tab" type="button" role="tab" data-bs-toggle="tab" data-bs-target="#photo" aria-controls="photo" aria-selected="false">Фото</button></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo Sanitize::sanitizeAttrValue($content_obj->getUrl()); ?>" target="_blank" rel="noopener">Просмотр&nbsp;<sup><span
                                class="fa fa-external-link"></span></sup></a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo LoggerRender::getLoggerLinkForEntityObj($content_obj); ?>"
                                       target="_blank" rel="noopener">Журнал&nbsp;<sup><span
                                class="fa fa-external-link"></span></sup></a></li>
        </ul>
        <p></p>
        <div class="tab-content pt-3">
            <div role="tabpanel" class="tab-pane fade show active" id="content" aria-labelledby="content-tab" tabindex="0">
                <div class="row mb-3">
                    <label for="title" class="col-md-2 col-form-label">Заголовок</label>

                    <div class="col-md-10">
                        <input type="text" class="form-control" id="title" name="title"
                               value="<?php echo Sanitize::sanitizeAttrValue($content_obj->getTitle()); ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="annotation" class="col-md-2 col-form-label">Анонс</label>

                    <div class="col-md-10">
                        <?php
                        echo RichTextEditor::create('annotation', $content_obj->getAnnotation(), 150, Content::CONTENT_FILES_DIR);
                        ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <?php
                        echo RichTextEditor::create('body', $content_obj->getBody(), 500, Content::CONTENT_FILES_DIR);
                        ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="template_id" class="col-md-2 col-form-label">Шаблон</label>

                    <div class="col-md-10">
                        <?php
                        $templates_ids_arr = $template_service->getAllIdsArrByIdAsc();
                        ?>
                        <select id="template_id" name="template_id" class="form-select">
                            <option value="">Шаблон по-умолчанию</option>
                            <?php
                            foreach ($templates_ids_arr as $template_id) {
                                $template_obj = $template_service->getById($template_id);
                                ?>
                                <option value="<?php echo $template_id; ?>"<?php echo(($content_obj->getTemplateId() == $template_id) ? ' selected' : ''); ?>><?php echo Sanitize::sanitizeTagContent($template_obj->getTitle()); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="image_file" class="col-md-2 col-form-label">Изображение</label>
                    <div class="col-md-10">
                        <?php
                        if ($content_obj->getImage()) {
                            $image_path = $content_service->getImagePath($content_obj);

                            ?>
                            <script type="text/javascript">
                                $(document).ready(function () {
                                GLightbox({selector: '[data-glightbox]'});
                                });
                            </script>

                            <div class="mb-3" id="image_area">
                                <a id="image" data-glightbox
                                   href="<?php echo ImageManager::getImgUrlByFileName($image_path) . '?d=' . time(); ?>">
                                    <img src="<?php echo ImageManager::getImgUrlByPreset($image_path,'120_auto') . '?d=' . time(); ?>" class="img-fluid img-thumbnail">
                                </a>
                                <a href="#" id="image_delete">Удалить</a>
                            </div>
                            <?php
                        }
                        ?>
                        <input class="form-control" type="file" name="image_file" id="image_file" accept="image/*">
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="publish" aria-labelledby="publish-tab" tabindex="0">
                <div class="row mb-3">
                    <label for="created_at" class="col-md-2 col-form-label">Дата создания</label>

                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text"><span class="fa fa-calendar"></span></span>
                            <input type="datetime-local" class="form-control" id="created_at" name="created_at"
                                   step="1"
                                   value="<?php echo($content_obj->getId() ? date('Y-m-d\TH:i:s', $content_obj->getCreatedAtTs()) : date('Y-m-d\TH:i:s')); ?>">
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="url" class="col-md-2 col-form-label">Адрес материала, URL</label>

                    <div class="col-md-10">
                        <input type="text" class="form-control" id="url" name="url"
                               value="<?php echo Sanitize::sanitizeAttrValue($content_obj->getUrl()); ?>"<?php echo($content_obj->isPublished() ? ' readonly' : ''); ?>>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="published_at" class="col-md-2 col-form-label">Показывать с</label>

                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text"><span class="fa fa-calendar"></span></span>
                            <input type="date" class="form-control" id="published_at" name="published_at"
                                   value="<?php echo $published_at ? substr($published_at, 0, 10) : ''; ?>">
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="unpublished_at" class="col-md-2 col-form-label">Показывать по</label>

                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text"><span class="fa fa-calendar"></span></span>
                            <input type="date" class="form-control" id="unpublished_at" name="unpublished_at"
                                   value="<?php echo $unpublished_at ? substr($unpublished_at, 0, 10) : ''; ?>">
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="offset-md-2 col-md-10">
                        <div class="form-check">
                            <input class="form-check-input" id="is_published" type="checkbox"
                                       name="is_published"<?php echo($content_obj->isPublished() ? ' checked' : ''); ?>
                                       value="1">
                            <label class="form-check-label" for="is_published">Опубликовано</label>
                        </div>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="rubrics" aria-labelledby="rubrics-tab" tabindex="0">
                <div class="row mb-3">
                    <label for="rubrics_arr" class="col-md-2 col-form-label">Рубрики</label>
                    <div class="col-md-10">
                        <select id="rubrics_arr" name="rubrics_arr[]" multiple class="form-select">
                            <?php
                            foreach ($rubric_ids_arr as $rubric_id) {
                                $rubric_obj = $rubric_service->getById($rubric_id);
                                ?>
                                <option value="<?php echo $rubric_obj->getId(); ?>"<?php echo (($content_obj->getId() && $content_service->hasRubricId($content_obj, $rubric_id)) ? ' selected' : ''); ?>><?php echo Sanitize::sanitizeTagContent($rubric_obj->getName()); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="main_rubric" class="col-md-2 col-form-label">Главная рубрика</label>
                    <div class="col-md-10">
                        <select id="main_rubric" name="main_rubric" class="form-select">
                            <option value=""></option>
                            <?php
                            foreach ($rubric_ids_arr as $rubric_id) {
                                $rubric_obj = $rubric_service->getById($rubric_id);
                                ?>
                                <option value="<?php echo $rubric_obj->getId(); ?>"<?php echo($rubric_id == $content_obj->getMainRubricId() ? ' selected' : ''); ?>><?php echo Sanitize::sanitizeTagContent($rubric_obj->getName()); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="seo" aria-labelledby="seo-tab" tabindex="0">
                <div class="row mb-3">
                    <label for="description" class="col-md-2 col-form-label">Описание</label>

                    <div class="col-md-10">
                        <input type="text" class="form-control" id="description" name="description"
                               value="<?php echo Sanitize::sanitizeAttrValue($content_obj->getDescription()); ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="keywords" class="col-md-2 col-form-label">Ключевые слова</label>

                    <div class="col-md-10">
                        <input type="text" class="form-control" id="keywords" name="keywords"
                               value="<?php echo Sanitize::sanitizeAttrValue($content_obj->getKeywords()); ?>">
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="photo" aria-labelledby="photo-tab" tabindex="0">
                <div class="offset-md-1 col-md-11">
                    <div class="card">
                        <div class="card-body">
                            <?php echo PhpRender::renderLocalTemplate(
                                'content_photo_form_edit.tpl.php',
                                [
                                    'content_type' => $content_type,
                                    'content_id' => $content_id,
                                    'content_service' => $content_service
                                ]
                            )
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="offset-md-2 col-md-10">
            <input type="submit" class="btn btn-primary" value="Сохранить изменения">
        </div>
    </div>
</form>

<script type="text/javascript">
    $("#image_delete").click(function () {
        $.ajax({
            type: "POST",
            url: "<?php echo Router::urlFor(AdminContentDeleteImageAction::class, ['content_type' => $content_type, 'content_id' => $content_id]); ?>",
            success: function (data) {
                $("#image_area").html("");
            }
        });
    });
</script>
