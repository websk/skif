<?php
/**
 * @var $model_class_name - полное имя класса модели
 * @var $field_name - имя поля
 * @var $field_value - значение
 * @var $read_only - Если передан как true, запрещает редактирование поля (disabled)
 */

use WebSK\Model\InterfaceGetTitle;
use WebSK\Skif\CRUD\CRUDController;
use WebSK\Skif\CRUD\CRUDUtils;
use WebSK\Skif\CRUD\ModelReferenceWidget\ModelReferenceWidget;

$controller_obj = CRUDController::getControllerClassNameByModelClassName($model_class_name);

if (!isset($model_class_name) or empty($model_class_name)) {
    throw new \Exception('Необходимо определить параметр model_class_name в настройках виджета');
}

if (!isset($read_only)) {
    $read_only = false;
}

$additional_style = '';

$model_obj = ModelReferenceWidget::getModelObject($model_class_name, $field_value);
if ($model_obj) {
    if (!($model_obj instanceof InterfaceGetTitle)) {
        throw new \Exception('Модель ' . $model_class_name . ' должна реализовывать интерфейс \Skif\Model\InterfaceGetTitle');
    }

    $model_obj_title_text = $model_obj->getTitle();
    if (!CRUDUtils::stringCanBeUsedAsLinkText($model_obj_title_text)) {
        $model_obj_title_text = $field_value;
    }

    $model_obj_title = '<a href="' . $controller_obj::getEditUrl($model_class_name,
            $field_value) . '">' . $model_obj_title_text . '</a>';
} else {
    $model_obj_title = 'Объект с ID ' . $field_value . ' - не найден';
    $additional_style = ' style="color: grey"';
}

$disable_str = '';
if ($read_only) {
    $disable_str = ' disabled="disabled"';
}
?>
<div class="row">
    <div class="col-md-9 col-sm-9 col-xs-12">
        <p class="form-control-static"<?= $additional_style ?>
           id="modelid-title-<?= $field_name ?>"><?= $model_obj_title ?></p>
    </div>
    <div class="col-md-3 col-sm-3 col-xs-12">
        <input type="text"<?= $disable_str ?> class="form-control" value="<?= $field_value ?>"
               id="modelid-number-<?= $field_name ?>" placeholder="введите ID" name="<?= $field_name ?>">
    </div>
</div>
<script>
    $("#modelid-number-<?= $field_name ?>").on("keyup change", function (e) {
        e.preventDefault();
        var model_id = $(this).val();
        var modelid_title = $('#modelid-title-<?= $field_name ?>');
        modelid_title.css('color', '');
        if (model_id == '') {
            modelid_title.html('');
            return;
        }
        $.post("/crud/widget/get_model_title_by_id", {
            model_class_name: '<?= urlencode($model_class_name) ?>',
            model_id: model_id
        }, function (data) {
            if (!data.success) {
                modelid_title.css('color', 'red');
                modelid_title.html(data.error);
                return;
            }
            modelid_title.html('<a href="' + data.href + '">' + data.display_title + '</a>');

        }, 'json');
    });
</script>