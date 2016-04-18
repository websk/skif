<?php
/**
 * @var $model_class_name
 * @var $objs_ids_arr
 * @var $context_arr array
 * @var $filter
 * @var $list_title
 * @var $current_controller_obj
 */

\Skif\Utils::assert($model_class_name);

// готовим список полей, которые будем выводить в таблицу
$reflect = new \ReflectionClass($model_class_name);
$props_arr = array();

$crud_fields_list_arr = array();

foreach ($reflect->getProperties() as $prop_obj) {
    if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
        $prop_obj->setAccessible(true);
        $props_arr[] = $prop_obj;
    }
}

if (property_exists($model_class_name, 'crud_fields_list_arr') && (count($model_class_name::$crud_fields_list_arr) > 0)) {
    $crud_fields_list_arr = $model_class_name::$crud_fields_list_arr;

    foreach ($props_arr as $delta => $property_obj) {
        if (!array_key_exists($property_obj->getName(), $crud_fields_list_arr)) {
            unset($props_arr[$delta]);
        }
    }
}

$container_models_arr = array();
if (property_exists($model_class_name, 'crud_container_model')) {
    $container_models_arr = $model_class_name::$crud_container_model;
}
?>

<?php
if (isset($list_title)) {
    ?>
    <h2><?php echo $list_title; ?></h2>
    <?php
}
?>

<div>
    <?php
    $show_filtered_panel = false;
    if ($reflect->hasMethod('crudShowFilteredPanel')) {
        $show_filtered_panel = $model_class_name::crudShowFilteredPanel();
    } else if (isset($model_class_name::$crud_show_filtered_panel)) {
        $show_filtered_panel = $model_class_name::$crud_show_filtered_panel;
    }

    if ($show_filtered_panel) {
        ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-8">
                        <?php
                        if (isset($model_class_name::$crud_model_filtered_field_arr_for_list)) {
                            ?>
                            <form class="form-inline">
                                <?php
                                foreach ($model_class_name::$crud_model_filtered_field_arr_for_list as $field_name) {
                                    ?>
                                    <div class="form-group">
                                        <label><?php echo \Skif\CRUD\CRUDUtils::getTitleForField($model_class_name, $field_name) ?></label>

                                        <?php
                                        $obj = new $model_class_name;

                                        $context_field_value = '';
                                        if (array_key_exists($field_name, $_GET)) {
                                            if ($_GET[$field_name] != '') {
                                                $context_field_value = $_GET[$field_name];
                                            }
                                        }

                                        echo \Skif\CRUD\Widgets::renderFieldWithWidget($field_name, $obj, $context_field_value)
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="form-group">
                                    <input type="submit" value="Применить фильтр" class="btn btn-default">
                                </div>
                            </form>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="col-md-4">
                        <?php
                        if (isset($model_class_name::$crud_model_title_field)) {
                            if (isset($model_class_name::$crud_allow_search)) {
                                if ($model_class_name::$crud_allow_search == true) {
                                    ?>
                                    <form action="<?php echo \Skif\UrlManager::getUriNoQueryString(); ?>">
                                        <input name="filter" value="<?php echo $filter; ?>">
                                        <input type="submit" value="искать">
                                    </form>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>

    <?php
    if (\Skif\CRUD\CRUDUtils::canDisplayCreateButton($model_class_name, $context_arr)) {
        if (property_exists($model_class_name, 'crud_fast_create_field_name')) {
            // create fast add block

            echo \Skif\PhpTemplate::renderTemplateBySkifModule(
                'CRUD',
                'fast_create_form.tpl.php',
                array(
                    'model_class_name' => $model_class_name,
                    'context_arr' => $context_arr,
                    'current_controller_obj' => \Skif\CRUD\CRUDController::getControllerClassNameByModelClassName($model_class_name)
                )
            );
        } else {
            $button_title = 'Добавить';
            if (isset($model_class_name::$crud_create_button_title)) {
                $button_title = $model_class_name::$crud_create_button_title;
            }

            ?>
            <p class="padding_top_10 padding_bottom_10">
                <a href="<?php echo $current_controller_obj::getAddUrl($model_class_name)
                    . ($context_arr ? '?' . http_build_query(array('context_arr' => $context_arr)) : ''); ?>"
                   class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> <?php echo $button_title; ?>
                </a>
            </p>
            <?php
        }
    }
    ?>

    <?php
    $model_class_interfaces_arr = class_implements($model_class_name);

    $show_delete_button = false;
    if (array_key_exists('Skif\Model\InterfaceDelete', $model_class_interfaces_arr)) {
        $show_delete_button = true;
    }

    $show_url_button = false;
    if (array_key_exists('Skif\Model\InterfaceGetUrl', $model_class_interfaces_arr)) {
        $show_url_button = true;
    }

    if (isset($_GET['kss'])) {
        var_dump($objs_ids_arr);
    }

    if (count($objs_ids_arr) > 0) {
    ?>
    <div>
        <table class="table table-striped table-hover">
            <?php
            if ($crud_fields_list_arr) {
                ?>
                <colgroup>
                    <?php
                    foreach ($crud_fields_list_arr as $field_arr) {
                        ?>
                        <col<?php echo(array_key_exists('col_class', $field_arr) ? ' class="' . $field_arr['col_class'] . '"' : ''); ?>>
                        <?php
                    }
                    ?>
                </colgroup>
                <?php
            }
            ?>
            <thead>
            <tr>
                <?php
                foreach ($props_arr as $prop_obj) {
                    $table_title = \Skif\CRUD\CRUDUtils::getTitleForField($model_class_name, $prop_obj->getName());

                    $td_class = '';
                    if (array_key_exists($prop_obj->getName(), $crud_fields_list_arr)) {
                        $list_field_arr = $crud_fields_list_arr[$prop_obj->getName()];

                        if (array_key_exists('td_class', $list_field_arr)) {
                            $td_class = $list_field_arr['td_class'];
                        }
                    }
                    ?>
                    <th<?php echo($td_class ? ' class="' . $td_class . '"' : ''); ?>><?php echo $table_title; ?></th>
                    <?php
                }
                ?>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($objs_ids_arr as $obj_id) {
                $obj_obj = \Skif\CRUD\CRUDUtils::createAndLoadObject($model_class_name, $obj_id);
                ?>
                <tr>
                    <?php
                    foreach ($props_arr as $prop_obj) {
                        $link_field_key = array_search($prop_obj->getName(), array_values($container_models_arr));
                        $roles = \Skif\CRUD\Widgets::getFieldWidgetName($prop_obj->getName(), $obj_obj);

                        $title = '';

                        if ($link_field_key !== false) {
                            $container_array_keys = array_keys($container_models_arr);
                            $container_model = $container_array_keys[$link_field_key];

                            $container_obj = \Skif\CRUD\CRUDUtils::createAndLoadObject($container_model, $prop_obj->getValue($obj_obj));

                            if (method_exists($container_obj, 'getTitle')) {
                                $title .= $container_obj->getTitle() . ' ';
                            }

                            $title .= '(' . $container_obj->getId() . ')';
                        } else if ($roles == 'options') {
                            $role = \Skif\CRUD\Widgets::getFieldWidgetOptionsArr($prop_obj->getName(), $obj_obj);
                            if (array_key_exists($prop_obj->getValue($obj_obj), $role)) {
                                $title = $role[$prop_obj->getValue($obj_obj)];
                            }
                        } else {
                            $title = \Skif\CRUD\Widgets::renderListFieldWithWidget($prop_obj->getName(), $obj_obj);

                            /*
                             * если это поле с названием модели - делаем его значение ссылкой на редактирование
                             * если же значение не содержит видимымх символов - выводим кнопку редактирования (чтобы не остаться без ссылки)
                             */
                            if (property_exists($model_class_name, 'crud_model_title_field')) {
                                if ($prop_obj->getName() == $model_class_name::$crud_model_title_field) {
                                    if (\Skif\CRUD\CRUDUtils::stringCanBeUsedAsLinkText($title)) {
                                        $edit_url = $current_controller_obj::getEditUrl($model_class_name, $obj_id);
                                        $title = '<a href="' . $edit_url . '">' . $title . '</a>';
                                    }
                                }
                            }

                        }

                        $td_class = '';
                        if (array_key_exists($prop_obj->getName(), $crud_fields_list_arr)) {
                            $list_field_arr = $crud_fields_list_arr[$prop_obj->getName()];

                            if (array_key_exists('td_class', $list_field_arr)) {
                                $td_class = $list_field_arr['td_class'];
                            }
                        }
                        ?>
                        <td<?php echo($td_class ? ' class="' . $td_class . '"' : ''); ?>><?php echo $title; ?></td>
                        <?php
                    }

                    $edit_url = $current_controller_obj::getEditUrl($model_class_name, $obj_id);
                    $delete_url = $current_controller_obj::getDeleteUrl($model_class_name, $obj_id);
                    ?>
                    <td align="right">
                        <a href="<?php echo $edit_url; ?>" title="Редактировать"
                           class="btn btn-outline btn-default btn-sm">
                            <span class="fa fa-edit fa-lg text-warning fa-fw"></span>
                        </a>
                        <?php
                        if ($show_url_button) {
                            ?>
                            <a href="<?php echo $obj_obj->getUrl(); ?>" target="_blank" title="Просмотр"
                               class="btn btn-outline btn-default btn-sm">
                                <span class="fa fa-external-link fa-lg text-info fa-fw"></span>
                            </a>
                            <?php
                        }

                        if ($show_delete_button) {
                            ?>
                            <a href="<?php echo $delete_url . '?destination=' . urlencode($_SERVER['REQUEST_URI']); ?>"
                               onClick="return confirm('Вы уверены, что хотите удалить?')" title="Удалить"
                               class="btn btn-outline btn-default btn-sm">
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
        }
        ?>
    </div>
