<?php
/**
 * @var $content_type_id
 * @var $rubric_id
 */

use Skif\Content\ContentType;
use Skif\Content\Rubric;
use Skif\Content\RubricController;
use Skif\Content\Template;
use Skif\Content\TemplateUtils;
use WebSK\Skif\CKEditor\CKEditor;

$content_type_obj = ContentType::factory($content_type_id);

if ($rubric_id == 'new') {
    $rubric_obj = new Rubric();
} else {
    $rubric_obj = Rubric::factory($rubric_id);
}
?>
<form action="<?php echo RubricController::getRubricsListUrlByContentType($content_type_obj->getType());?>/save/<?php echo $rubric_id; ?>" method="post" class="form-horizontal">
    <div class="form-group">
        <label class="col-md-4 control-label">Название</label>

        <div class="col-md-8">
            <input type="text" name="name" value="<?= $rubric_obj->getName() ?>" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Комментарий</label>

        <div class="col-md-8">
            <?php
            echo CKEditor::createBasicCKEditor('comment', $rubric_obj->getComment(), 150, 'content');
            ?>
        </div>
    </div>
    <div class="form-group">
        <label for="template_id" class="col-md-2 control-label">Шаблон</label>

        <div class="col-md-10">
            <?php
            $templates_ids_arr = TemplateUtils::getTemplatesIdsArr();
            ?>
            <select id="template_id" name="template_id" class="form-control">
                <option value="0">Шаблон по-умолчанию</option>
                <?
                foreach ($templates_ids_arr as $template_id) {
                    $template_obj = Template::factory($template_id);
                    ?>
                    <option value="<?php echo $template_id; ?>"<?php echo (($rubric_obj->getTemplateId() == $template_id) ? ' selected' : ''); ?>><?php echo $template_obj->getTitle(); ?></option>
                    <?
                }
                ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="url" class="col-md-2 control-label">Адрес материала, URL</label>

        <div class="col-md-10">
            <input type="text" class="form-control" id="url" name="url" value="<?php echo $rubric_obj->getUrl(); ?>">
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-offset-4 col-md-8">
            <input type="submit" value="Сохранить изменения" class="btn btn-primary">
        </div>
    </div>
</form>

