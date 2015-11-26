<?php
/**
 * @var $model_class_name
 */

$current_controller_obj = \Skif\UrlManager::getCurrentControllerObj();

// чтобы создать форму быстрого добавления в классе должны быть следующие поля:
// public static $crud_fast_create_field_name = 'answer_text', где answer_text - имя выводимого поля

if (!property_exists($model_class_name, 'crud_fast_create_field_name')) {
    return;
}

$fast_create_field_name = $model_class_name::$crud_fast_create_field_name;

$label_field_name = \Skif\CRUD\CRUDUtils::getTitleForField($model_class_name, $fast_create_field_name);
$create_url = $current_controller_obj::getCreateUrl($model_class_name);

echo '<form role="form" method="post" class="form-inline" action="' . $create_url . '">';
echo '<div class="form-group">';
echo '<input placeholder="' . $label_field_name . '" name="' . $fast_create_field_name . '" class="form-control"/>';
echo '<button type="submit" class="btn btn-default">Добавить</button>';

foreach ($context_arr as $context_arr_key => $context_arr_value) {
    echo '<input type="hidden" name="' . $context_arr_key . '" value="' . $context_arr_value . '">';
}

echo '<input type="hidden" name="destination" value="' . \Skif\UrlManager::getUriNoQueryString() . '">';
echo '</div>';
echo '</form>';
