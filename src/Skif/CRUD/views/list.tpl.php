<?php
/**
 * @var $model_class_name
 * @var $context_arr array
 * @var $list_title string
 */

\Skif\Utils::assert($model_class_name);

//
// готовим список ID объектов для вывода
//

$filter = '';
if (isset($_GET['filter'])){
    $filter = $_GET['filter'];
}
$objs_ids_arr = \Skif\CRUD\CRUDUtils::getObjIdsArrayForModel($model_class_name, $context_arr, $filter);

//
// готовим список полей, которые будем выводить в таблицу
//

$reflect = new \ReflectionClass($model_class_name);
$props_arr = array();

$crud_table_fields_arr = array();

foreach ($reflect->getProperties() as $prop_obj) {
    if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
        $prop_obj->setAccessible(true);
        $props_arr[] = $prop_obj;
    }
}

if (property_exists($model_class_name, 'crud_table_fields_arr') && (count($model_class_name::$crud_table_fields_arr) > 0)) {
    foreach ($props_arr as $delta => $property_obj) {
        if (!in_array($property_obj->getName(), $model_class_name::$crud_table_fields_arr)) {
            unset($props_arr[$delta]);
        }
    }
}

$container_models_arr = array();
if (property_exists($model_class_name, 'crud_container_model')) {
    $container_models_arr = $model_class_name::$crud_container_model;
}

//
// вывод таблицы
//

echo '<div class="spb_admin_section">';
echo '<h2 class="pull-left">' . $list_title;
if (\Skif\CRUD\CRUDUtils::canDisplayCreateButton($model_class_name, $context_arr)) {
    echo ' <a style="font-size: 75%;" class="glyphicon glyphicon-plus" href="/crud/add/' . urlencode($model_class_name) . '?' . http_build_query(array('context_arr' => $context_arr)) . '"></a>';
}

echo '</h2>';


if (isset($model_class_name::$crud_model_title_field)) {
    if (isset($model_class_name::$crud_allow_search)) {
        if ($model_class_name::$crud_allow_search == true) {
            echo '<div class="pull-right" style="margin-top: 25px;"><form action="' . \Skif\Helpers::uri_no_getform() . '"><input name="filter" value="' . $filter . '"><input type="submit" value="искать"></form></div>';
        }
    }
}


echo '<div class="clearfix"></div>';


// create fast add block

// чтобы создать форму быстрого добавления в классе должны быть следующие поля:
// public static $crud_fast_create_field_name = 'answer_text', где answer_text - имя выводимого поля
if (property_exists($model_class_name, 'crud_fast_create_field_name')) {
    $fast_create_field_name = $model_class_name::$crud_fast_create_field_name;

    $label_field_name = \Skif\CRUD\CRUDUtils::getTitleForField($model_class_name, $fast_create_field_name);
    $create_url = \Skif\CRUD\ControllerCRUD::getCreateUrl($model_class_name);

    echo '<form role="form" method="post" class="form-inline" action="' . $create_url . '">';
    echo '<div class="form-group">';
    echo '<input placeholder="' . $label_field_name . '" name="' . $fast_create_field_name . '" class="form-control"/>';
    echo '<button type="submit" class="btn btn-default">Добавить</button>';

    foreach ($context_arr as $context_arr_key => $context_arr_value) {
        echo '<input type="hidden" name="' . $context_arr_key . '" value="' . $context_arr_value . '">';
    }

    echo '<input type="hidden" name="destination" value="' . \Skif\Helpers::uri_no_getform() . '">';
    echo '</div>';
    echo '</form>';

}

if (count($objs_ids_arr) > 0) {

    ?>
    <div>
    <table class="table table-striped table-hover">
    <thead>
<tr>
    <?php

    foreach ($props_arr as $prop_obj) {
        $table_title = \Skif\CRUD\CRUDUtils::getTitleForField($model_class_name, $prop_obj->getName());
        echo '<th>' . $table_title . '</th>';
    }
    ?>
    <th></th>
</tr>
    </thead>
    <tbody>
<?php
    foreach ($objs_ids_arr as $obj_id) {
        $obj_obj = \Skif\CRUD\CRUDUtils::createAndLoadObject($model_class_name, $obj_id);

        $show_edit_button = true;

        echo '<tr>';
        foreach ($props_arr as $prop_obj) {
            $link_field_key = array_search($prop_obj->getName(), array_values($container_models_arr));
			$roles = \Skif\CRUD\Widgets::getFieldWidgetName($prop_obj->getName(), $obj_obj);

            $title = "";

            if ($link_field_key !== false) {
                $container_array_keys = array_keys($container_models_arr);
                $container_model = $container_array_keys[$link_field_key];

                $container_obj = \Skif\CRUD\CRUDUtils::createAndLoadObject($container_model, $prop_obj->getValue($obj_obj));

                if (method_exists($container_obj, 'getTitle')) {
                    $title .= $container_obj->getTitle() . " ";
                }

                $title .= "(" . $container_obj->getId() . ")";
            }
            else if ($roles == "options") {
				$role = \Skif\CRUD\Widgets::getFieldWidgetOptionsArr($prop_obj->getName(), $obj_obj);
                if (array_key_exists($prop_obj->getValue($obj_obj), $role)) {
                    $title = $role[$prop_obj->getValue($obj_obj)];
                }
            }
			else {
                $title = \Skif\CRUD\Widgets::renderListFieldWithWidget($prop_obj->getName(), $obj_obj);

                /*
                 * если это поле с названием модели - делаем его значение ссылкой на редактирование
                 * если же значение не содержит видимымх символов - выводим кнопку редактирования (чтобы не остаться без ссылки)
                 */
                if (property_exists($model_class_name, 'crud_model_title_field')) {
                    if ($prop_obj->getName() == $model_class_name::$crud_model_title_field){
                        if (\Skif\CRUD\CRUDUtils::stringCanBeUsedAsLinkText($title)) {
                            $edit_url = \Skif\CRUD\ControllerCRUD::getEditUrl($model_class_name, $obj_id);
                            $title = '<a href="' . $edit_url . '">' . $title . '</a>';
                            $show_edit_button = false;
                        }
                    }
                }

            }

            echo '<td>' . $title . '</td>';
        }

        $edit_url = \Skif\CRUD\ControllerCRUD::getEditUrl($model_class_name, $obj_id);
        $delete_url = \Skif\CRUD\ControllerCRUD::getDeleteUrl($model_class_name, $obj_id);
        ?>
        <td align="right">
            <?php
            if ($show_edit_button) {
                ?>
                <a href="<?php echo $edit_url; ?>"
                   title="Редактировать" class="btn btn-outline btn-default btn-sm">
                    <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
                </a>
                <?php
            }

            $delete_disabled = false;
            $model_class_interfaces_arr = class_implements($model_class_name);
            if (!array_key_exists('Skif\Model\InterfaceDelete', $model_class_interfaces_arr)) {
                $delete_disabled = true;
            }

            if (!$delete_disabled) {
                ?>
                <a href="<?php echo $delete_url . '?destination=' . urlencode($_SERVER['REQUEST_URI']); ?>" onClick="return confirm('Вы уверены, что хотите удалить?')" title="Удалить" class="btn btn-outline btn-default btn-sm">
                    <span class="fa fa-trash-o fa-lg text-danger fa-fw"></span>
                </a>
            <?php
            }
            ?>
        </td>
</tr>
        <?php
    }
    ?>
        </tbody>
    </table>
    <?php
    echo \Skif\Pager::renderPager(count($objs_ids_arr));
    ?>
<?php
}
?>
</div>

