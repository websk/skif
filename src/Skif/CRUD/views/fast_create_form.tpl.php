<?php
/**
 * @var $model_class_name
 * @var $context_arr
 */

$current_controller_obj = \Skif\UrlManager::getCurrentControllerObj();

// чтобы создать форму быстрого добавления в классе должны быть следующие поля:
// public static $crud_fast_create_field_name = 'answer_text', где answer_text - имя выводимого поля

if (!property_exists($model_class_name, 'crud_fast_create_field_name')) {
    return;
}

$obj = new $model_class_name;

$fast_create_field_name = $model_class_name::$crud_fast_create_field_name;

$label_field_name = \Skif\CRUD\CRUDUtils::getTitleForField($model_class_name, $fast_create_field_name);
$create_url = $current_controller_obj::getCreateUrl($model_class_name);
?>
<form role="form" method="post" class="form" action="<?php echo $create_url; ?>">
    <div class="form-group">
        <label><?php echo $label_field_name; ?></label>
        <?php
        echo \Skif\CRUD\Widgets::renderFieldWithWidget($fast_create_field_name, $obj);

        foreach ($context_arr as $context_arr_key => $context_arr_value) {
            echo '<input type="hidden" name="' . $context_arr_key . '" value="' . $context_arr_value . '">';
        }
        ?>
        <input type="hidden" name="destination" value="<?php echo \Skif\UrlManager::getUriNoQueryString(); ?>">
    </div>
    <div class="form-group">
        <?php
        $button_title = 'Добавить';
        if (isset($model_class_name::$crud_create_button_title)) {
            $button_title = $model_class_name::$crud_create_button_title;
        }
        ?>
        <button type="submit" class="btn btn-default"><?php echo $button_title; ?></button>
    </div>
</form>
