<?php
/**
 * @var $obj object
 * @var $model_class_name
 * @var $current_controller_obj
 */

use WebSK\Skif\CRUD\CRUDController;
use WebSK\Skif\CRUD\CRUDUtils;
use WebSK\Skif\CRUD\Widgets;
use WebSK\Model\InterfaceSave;
use WebSK\Utils\Assert;
use WebSK\Views\PhpRender;

Assert::assert($obj);
$model_class_name = get_class($obj);

$reflect = new \ReflectionClass($model_class_name);

$props_arr = array();

foreach ($reflect->getProperties() as $prop_obj) {
    if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
        $prop_obj->setAccessible(true);
        $props_arr[] = $prop_obj;
    }
}

$crud_editor_fields_arr = CRUDUtils::getCrudEditorFieldsArrForClass($model_class_name);
if ($crud_editor_fields_arr) {
    foreach ($props_arr as $delta => $property_obj) {
        if (!array_key_exists($property_obj->getName(), $crud_editor_fields_arr)) {
            unset($props_arr[$delta]);
        }
    }
}

if ($obj instanceof InterfaceSave) {
    ?>
    <div>
        <form id="form" role="form" method="post"
              action="<?php echo $current_controller_obj::getSaveUrl($model_class_name, $obj->getId()); ?>"
              class="form-horizontal">
            <?php
            foreach ($props_arr as $prop_obj) {
                $editor_title = CRUDUtils::getTitleForField($model_class_name, $prop_obj->getName());
                $editor_description = CRUDUtils::getDescriptionForField($model_class_name, $prop_obj->getName());
                $required = CRUDUtils::isRequiredField($model_class_name, $prop_obj->getName());
                ?>
                <div class="form-group <?= (($required) ? 'required' : '') ?>">
                    <label for="<?php echo $prop_obj->getName() ?>"
                           class="col-md-2 control-label"><?= $editor_title ?></label>

                    <div class="col-md-10">
                        <?php
                        echo Widgets::renderFieldWithWidget($prop_obj->getName(), $obj);

                        if ($editor_description) {
                            ?>
                            <span class="help-block">
                            <?php echo $editor_description; ?>
                        </span>
                        <?php
                        }
                        ?>
                    </div>
                </div>

            <?php
            }
            ?>

            <?php
            if (array_key_exists("destination", $_REQUEST)) {
                echo '<input type="hidden" name="destination" value="' . $_REQUEST["destination"] . '">';
            }
            ?>
            <div class="form-group">
                <div class="col-md-10 col-md-offset-2">
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    <a href="<?php echo $current_controller_obj::getDeleteUrl($model_class_name, $obj->getId()); ?>" class="btn btn-danger pull-right" onclick="return deleteConfirm();">Удалить</a>
                </div>
            </div>
        </form>

        <script>
            function deleteConfirm() {
                return confirm("Вы действительно хотите удалить <?php echo CRUDUtils::getModelTitleForObj($obj); ?>?");
            }

            $('#form').on('submit', function (e) {
                $(this).find('.required').removeClass('has-error').each(function () {
                    if ($(this).find('input, textarea, select').val() === '') {
                        $(this).addClass('has-error');
                    }
                });

                if ($(this).find('.required').is('.has-error')) {
                    alert('Заполните обязательные поля!');
                    e.preventDefault();
                }
            });
        </script>
    </div>
    <?php
}

// Вывод приязанных объектов

if (property_exists($model_class_name, 'related_models_arr')) {
    foreach ($model_class_name::$related_models_arr as $related_model_class_name => $related_model_data) {
        if (array_key_exists('not_show_in_crud_edit_form', $related_model_data)
            && ($related_model_data['not_show_in_crud_edit_form'] === true)) {
            continue;
        }

        Assert::assert(array_key_exists('link_field', $related_model_data));

        $relation_field_name = $related_model_data['link_field'];

        $list_title = "Связанные данные " . $related_model_class_name;

        if (array_key_exists('list_title', $related_model_data)) {
            $list_title = $related_model_data['list_title'];
        }

        $context_arr = array($relation_field_name => $obj->getId());

        if (isset($related_model_data['context_fields_arr'])
            && is_array($related_model_data['context_fields_arr'])
        ) {
            foreach ($related_model_data['context_fields_arr'] as $context_field) {
                $context_arr[$context_field] = CRUDUtils::getObjectFieldValue($obj, $context_field);
            }
        }

        echo '<hr>';

        $objs_ids_arr = CRUDUtils::getObjIdsArrayForModel($related_model_class_name, $context_arr);

        echo PhpRender::renderLocalTemplate(
            'list.tpl.php',
            array(
                'model_class_name' => $related_model_class_name,
                'objs_ids_arr' => $objs_ids_arr,
                'context_arr' => $context_arr,
                'list_title' => $list_title,
                'current_controller_obj' => CRUDController::getControllerClassNameByModelClassName($related_model_class_name)
            )
        );
    }
}
