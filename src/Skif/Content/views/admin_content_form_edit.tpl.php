<?php
/**
 * @var $content_id
 * @var $content_type
 */

if ($content_id == 'new') {
    $content_obj = new \Skif\Content\Content();
} else {
    $content_obj = \Skif\Content\Content::factory($content_id);
}
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

<form class="form-horizontal" id="content_edit_form" action="/admin/content/<?php echo $content_type; ?>/save/<?php echo $content_id; ?>" enctype="multipart/form-data" method="post">
    <div role="tabpanel">

        <ul id="contentTab" class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#content" id="content-tab" role="tab" data-toggle="tab" aria-controls="content">Контент</a></li>
            <li role="presentation"><a href="#publish" role="tab" id="publish-tab" data-toggle="tab" aria-controls="publish">Настройки публикации</a></li>
            <li role="presentation"><a href="#seo" role="tab" id="seo-tab" data-toggle="tab" aria-controls="seo">SEO</a></li>
            <li role="presentation"><a href="<?php echo $content_obj->getUrl(); ?>" role="tab" target="_blank">Просмотр&nbsp;<sup><span class="glyphicon glyphicon-new-window"></span></sup></a></li>
            <li role="presentation"><a href="/admin/logger/object_log/<?= urlencode(\Skif\Utils::getFullObjectId($content_obj));?>" target="_blank">Журнал&nbsp;<sup><span class="glyphicon glyphicon-new-window"></span></sup></a></li>
        </ul>
        <p></p>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="content">
                <div class="form-group">
                    <label for="title" class="col-md-2 control-label">Заголовок</label>

                    <div class="col-md-10">
                        <input type="text" class="form-control" id="title" name="title"
                               value="<?php echo $content_obj->getTitle(); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="annotation" class="col-md-2 control-label">Анонс</label>

                    <div class="col-md-10">
                        <?php
                        echo \Skif\CKEditor::createBasicCKEditor('annotation', $content_obj->getAnnotation(), 150, 'content');
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <?php
                        echo \Skif\CKEditor::createFullCKEditor('body', $content_obj->getBody(), 500, 'content');
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="template_id" class="col-md-2 control-label">Шаблон</label>

                    <div class="col-md-10">
                        <?php
                        $templates_ids_arr = \Skif\Content\ContentUtils::getTemplatesIdsArr();
                        ?>
                        <select id="template_id" name="template_id" class="form-control">
                            <option value="0">Шаблон по-умолчанию</option>
                            <?
                            foreach ($templates_ids_arr as $template_id) {
                                $template_obj = \Skif\Content\Template::factory($template_id);
                                ?>
                                <option value="<?php echo $template_id; ?>"<?php echo (($content_obj->getTemplateId() == $template_id) ? ' selected' : ''); ?>><?php echo $template_obj->getName(); ?></option>
                            <?
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
                            ?>
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("a#image").fancybox({
                                    });
                                });
                            </script>

                            <div class="form-group" id="image_area">
                                <a id="image" href="<?php echo \Skif\Image\ImageManager::getImgUrlByFileName($content_obj->getImagePath()) . '?d=' . time(); ?>">
                                    <img src="<?php echo  \Skif\Image\ImageManager::getImgUrlByPreset($content_obj->getImagePath(), '120_auto') . '?d=' . time(); ?>" class="img-responsive img-thumbnail" border="0">
                                </a>
                                <a href="#image_delete" id="image_delete">Удалить</a>
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
                            <input type="text" class="form-control" id="created_at" name="created_at" value="<?php echo ($content_obj->getCreatedAt() ? $content_obj->getCreatedAt() : date('Y-m-d H:i:s')); ?>">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="url" class="col-md-2 control-label">Адрес материала, URL</label>

                    <div class="col-md-10">
                        <input type="text" class="form-control" id="url" name="url" value="<?php echo $content_obj->getUrl(); ?>"<?php echo ($content_obj->isPublished() ? ' disabled' : ''); ?>>
                    </div>
                </div>

                <div class="form-group">
                    <label for="published_at" class="col-md-2 control-label">Показывать с</label>

                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            <input type="text" class="form-control" id="published_at" name="published_at" value="<?php echo $content_obj->getPublishedAt(); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="unpublished_at" class="col-md-2 control-label">Показывать по</label>

                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            <input type="text" class="form-control" id="unpublished_at" name="unpublished_at" value="<?php echo $content_obj->getUnpublishedAt(); ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-offset-2 col-md-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_published"<?php echo ($content_obj->isPublished() ? ' checked' : ''); ?> value="1">
                                Опубликовано
                            </label>
                        </div>
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
        </div>

        <div class="form-group">
            <div class="col-md-offset-2 col-md-10">
                <input type="submit" class="btn btn-primary" value="Сохранить изменения">
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
    $("#image_delete").click(function () {
        $.ajax({
            type: "POST",
            url: "/admin/content/<?php echo $content_type; ?>/delete_image/<?php echo $content_id; ?>",
            success: function(data) {
                $("#image_area").html("");
            }
        });
    });
</script>
