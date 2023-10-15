<?php
/**
 * @var int $content_id
 * @var string $content_type
 */

use WebSK\Skif\Content\Content;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\CKEditor\CKEditor;
use WebSK\Image\ImageManager;
use WebSK\Logger\LoggerRender;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentDeleteImageAction;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentSaveHandler;
use WebSK\Slim\Container;
use WebSK\Slim\Router;
use WebSK\Views\PhpRender;

$container = Container::self();

$content_type_service = ContentServiceProvider::getContentTypeService($container);
$content_type_obj = $content_type_service->getByType($content_type);
$content_service = ContentServiceProvider::getContentService($container);

if ($content_id == 'new') {
    $content_obj = new Content();
} else {
    $content_obj = $content_service->getById($content_id);
}

$rubric_service = ContentServiceProvider::getRubricService($container);

$rubric_ids_arr = $rubric_service->getIdsArrByContentTypeId($content_type_obj->getId());
?>
<script type="text/javascript">
    $(function () {
        $('#created_at').datetimepicker({
            locale: 'ru',
            format: 'YYYY-MM-DD HH:mm:ss'
        });
        $('#published_at').datetimepicker({
            locale: 'ru',
            format: 'YYYY-MM-DD'
        });
        $('#unpublished_at').datetimepicker({
            locale: 'ru',
            format: 'YYYY-MM-DD'
        });
    });

    $('#contentTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

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

<form class="form-horizontal" id="content_edit_form"
      action="<?php echo Router::pathFor(AdminContentSaveHandler::class, ['content_type' => $content_type, 'content_id' => $content_id]); ?>" enctype="multipart/form-data"
      method="post">
    <div role="tabpanel">

        <ul id="contentTab" class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#content" id="content-tab" role="tab" data-toggle="tab"
                                                      aria-controls="content">Контент</a></li>
            <li role="presentation"><a href="#publish" role="tab" id="publish-tab" data-toggle="tab"
                                       aria-controls="publish">Настройки публикации</a></li>
            <li role="presentation"><a href="#rubrics" role="tab" id="rubrics-tab" data-toggle="tab"
                                       aria-controls="rubrics">Рубрики</a></li>
            <li role="presentation"><a href="#seo" role="tab" id="seo-tab" data-toggle="tab" aria-controls="seo">SEO</a></li>
            <li role="presentation"><a href="#photo" role="tab" id="photo-tab" data-toggle="tab" aria-controls="photo">Фото</a></li>
            <li role="presentation"><a href="<?php echo $content_obj->getUrl(); ?>" role="tab" target="_blank">Просмотр&nbsp;<sup><span
                                class="glyphicon glyphicon-new-window"></span></sup></a></li>
            <li role="presentation"><a href="<?php echo LoggerRender::getLoggerLinkForEntityObj($content_obj); ?>"
                                       target="_blank">Журнал&nbsp;<sup><span
                                class="glyphicon glyphicon-new-window"></span></sup></a></li>
        </ul>
        <p></p>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="content">
                <div class="form-group">
                    <label for="title" class="col-md-2 control-label">Заголовок</label>

                    <div class="col-md-10">
                        <input type="text" class="form-control" id="title" name="title"
                               value="<?php echo $content_obj->getTitle(); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="annotation" class="col-md-2 control-label">Анонс</label>

                    <div class="col-md-10">
                        <?php
                        echo CKEditor::createCKEditor('annotation', $content_obj->getAnnotation(), 150, Content::CONTENT_FILES_DIR);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <?php
                        echo CKEditor::createCKEditor('body', $content_obj->getBody(), 500, Content::CONTENT_FILES_DIR);
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="template_id" class="col-md-2 control-label">Шаблон</label>

                    <div class="col-md-10">
                        <?php
                        $template_service = ContentServiceProvider::getTemplateService(Container::self());

                        $templates_ids_arr = $template_service->getAllIdsArrByIdAsc();
                        ?>
                        <select id="template_id" name="template_id" class="form-control">
                            <option value="">Шаблон по-умолчанию</option>
                            <?php
                            foreach ($templates_ids_arr as $template_id) {
                                $template_obj = $template_service->getById($template_id);
                                ?>
                                <option value="<?php echo $template_id; ?>"<?php echo(($content_obj->getTemplateId() == $template_id) ? ' selected' : ''); ?>><?php echo $template_obj->getTitle(); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="image_file" class="col-md-2 control-label">Изображение</label>
                    <div class="col-md-10">
                        <?php
                        if ($content_obj->getImage()) {
                            $image_path = $content_service->getImagePath($content_obj);

                            ?>
                            <script type="text/javascript">
                                $(document).ready(function () {
                                    $("a#image").fancybox({});
                                });
                            </script>

                            <div class="form-group" id="image_area">
                                <a id="image"
                                   href="<?php echo ImageManager::getImgUrlByFileName($image_path) . '?d=' . time(); ?>">
                                    <img src="<?php echo ImageManager::getImgUrlByPreset($image_path,'120_auto') . '?d=' . time(); ?>" class="img-responsive img-thumbnail">
                                </a>
                                <a href="#" id="image_delete">Удалить</a>
                            </div>
                            <?php
                        }
                        ?>
                        <input type="file" name="image_file" id="image_file">
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="publish">
                <div class="form-group">
                    <label for="created_at" class="col-md-2 control-label">Дата создания</label>

                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            <input type="text" class="form-control" id="created_at" name="created_at"
                                   value="<?php echo($content_obj->getId() ? date('Y-m-d H:i:s', $content_obj->getCreatedAtTs()) : date('Y-m-d H:i:s')); ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="url" class="col-md-2 control-label">Адрес материала, URL</label>

                    <div class="col-md-10">
                        <input type="text" class="form-control" id="url" name="url"
                               value="<?php echo $content_obj->getUrl(); ?>"<?php echo($content_obj->isPublished() ? ' readonly="readonly"' : ''); ?>>
                    </div>
                </div>

                <div class="form-group">
                    <label for="published_at" class="col-md-2 control-label">Показывать с</label>

                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            <input type="text" class="form-control" id="published_at" name="published_at"
                                   value="<?php echo $content_obj->getPublishedAt(); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="unpublished_at" class="col-md-2 control-label">Показывать по</label>

                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            <input type="text" class="form-control" id="unpublished_at" name="unpublished_at"
                                   value="<?php echo $content_obj->getUnpublishedAt(); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-offset-2 col-md-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox"
                                       name="is_published"<?php echo($content_obj->isPublished() ? ' checked' : ''); ?>
                                       value="1">
                                Опубликовано
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="rubrics">
                <div class="form-group">
                    <label for="rubrics_arr" class="col-md-2 control-label">Рубрики</label>
                    <div class="col-md-10">
                        <select id="rubrics_arr" name="rubrics_arr[]" multiple="multiple" class="form-control">
                            <?php
                            foreach ($rubric_ids_arr as $rubric_id) {
                                $rubric_obj = $rubric_service->getById($rubric_id);
                                ?>
                                <option value="<?php echo $rubric_obj->getId(); ?>"<?php echo (($content_obj->getId() && $content_service->hasRubricId($content_obj, $rubric_id)) ? ' selected' : ''); ?>><?php echo $rubric_obj->getName(); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="main_rubric" class="col-md-2 control-label">Главная рубрика</label>
                    <div class="col-md-10">
                        <select id="main_rubric" name="main_rubric" class="form-control">
                            <option value=""></option>
                            <?php
                            foreach ($rubric_ids_arr as $rubric_id) {
                                $rubric_obj = $rubric_service->getById($rubric_id);
                                ?>
                                <option value="<?php echo $rubric_obj->getId(); ?>"<?php echo($rubric_id == $content_obj->getMainRubricId() ? ' selected' : ''); ?>><?php echo $rubric_obj->getName(); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="seo">
                <div class="form-group">
                    <label for="description" class="col-md-2 control-label">Описание</label>

                    <div class="col-md-10">
                        <input type="text" class="form-control" id="description" name="description"
                               value="<?php echo $content_obj->getDescription(); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="keywords" class="col-md-2 control-label">Ключевые слова</label>

                    <div class="col-md-10">
                        <input type="text" class="form-control" id="keywords" name="keywords"
                               value="<?php echo $content_obj->getKeywords(); ?>">
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="photo">
                <div class="col-md-offset-1 col-md-11">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <?php echo PhpRender::renderLocalTemplate(
                                'content_photo_form_edit.tpl.php',
                                ['content_type' => $content_type, 'content_id' => $content_id]
                            )
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <input type="submit" class="btn btn-primary" value="Сохранить изменения">
        </div>
    </div>
</form>

<script type="text/javascript">
    $("#image_delete").click(function () {
        $.ajax({
            type: "POST",
            url: "<?php echo Router::pathFor(AdminContentDeleteImageAction::class, ['content_type' => $content_type, 'content_id' => $content_id]); ?>",
            success: function (data) {
                $("#image_area").html("");
            }
        });
    });
</script>
