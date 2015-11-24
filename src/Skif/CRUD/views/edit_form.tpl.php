<?php
/**
 * @var $obj object
 */

\Skif\Utils::assert($obj);
$model_class_name = get_class($obj);

$reflect = new \ReflectionClass($model_class_name);

$props_arr = array();

foreach ($reflect->getProperties() as $prop_obj) {
    if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
        $prop_obj->setAccessible(true);
        $props_arr[] = $prop_obj;
    }
}

$crud_editor_fields_arr = \Skif\CRUD\CRUDUtils::getCrudEditorFieldsArrForClass($model_class_name);
if ($crud_editor_fields_arr) {
    foreach ($props_arr as $delta => $property_obj) {
        if (!array_key_exists($property_obj->getName(), $crud_editor_fields_arr)) {
            unset($props_arr[$delta]);
        }
    }
}

echo \Skif\EditorTabs\Render::renderForObj($obj);

/*
if (property_exists($model_class_name, 'crud_extra_tabs') && count($model_class_name::$crud_extra_tabs) > 0) {
    ?>
    <ul class="nav nav-tabs" role="tablist">
        <?php
        foreach ($model_class_name::$crud_extra_tabs as $tab_pathname => $tab_title) {
            //$tab_url = $tab_pathname.'?full_object_id='.\Skif\CRUDUtils::getFullObjectId($obj);
            $tab_pathname = str_replace('#MODEL_ID#', $obj->getId(), $tab_pathname);
            $tab_pathname = str_replace('#MODEL_CLASS_NAME#', urlencode($model_class_name), $tab_pathname);

            $li_class = '';
            if ($tab_pathname == \Skif\CRUDUtils::uri_no_getform()){
                $li_class .= ' active ';
            }

            echo '<li class="' . $li_class . '"><a href="' . $tab_pathname . '">'.$tab_title.'</a></li>';
        }
        ?>
    </ul>
<?php
}
*/

if ($obj instanceof \Skif\Model\InterfaceSave) {
    ?>
    <div>
        <form id="form" role="form" method="post"
              action="/crud/save/<?php echo urlencode($model_class_name) ?>/<?php echo $obj->getId(); ?>"
              class="form-horizontal">
            <?php
            foreach ($props_arr as $prop_obj) {
                $editor_title = \Skif\CRUD\CRUDUtils::getTitleForField($model_class_name, $prop_obj->getName());
                $editor_description = \Skif\CRUD\CRUDUtils::getDescriptionForField($model_class_name, $prop_obj->getName());
                $required = \Skif\CRUD\CRUDUtils::isRequiredField($model_class_name, $prop_obj->getName());
                ?>
                <div class="form-group <?= (($required) ? 'required' : '') ?>">
                    <label for="<?php echo $prop_obj->getName() ?>"
                           class="col-md-2 control-label"><?= $editor_title ?></label>

                    <div class="col-md-10">
                        <?php
                        echo \Skif\CRUD\Widgets::renderFieldWithWidget($prop_obj->getName(), $obj);

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
            if (array_key_exists("destination_url", $_REQUEST)) {
                echo '<input type="hidden" name="destination" value="' . $_REQUEST["destination_url"] . '">';
            }
            ?>
            <div class="row">
                <div class="col-md-8 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        </form>

        <script>
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

// вывод приязанных объектов

if (property_exists($model_class_name, 'crud_related_models_arr')) {
    foreach ($model_class_name::$crud_related_models_arr as $related_model_class_name => $related_model_data) {
        $list_title = "Связанные данные " . $related_model_class_name;
        if (!is_array($related_model_data)) { // старая форма связи, потом удалить
            $relation_field_name = $related_model_data;
        } else {
            \Skif\Utils::assert(array_key_exists('link_field', $related_model_data));
            $relation_field_name = $related_model_data['link_field'];

            if (array_key_exists('list_title', $related_model_data)) {
                $list_title = $related_model_data['list_title'];
            }
        }

        $list_html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'CRUD',
            'list.tpl.php',
            array(
                'model_class_name' => $related_model_class_name,
                'context_arr' => array($relation_field_name => $obj->getId()),
                'list_title' => $list_title
            )
        );
    }
}
