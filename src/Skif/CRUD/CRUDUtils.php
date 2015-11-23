<?php

namespace Skif\CRUD;


class CRUDUtils
{
    /**
     * Возвращает "полное имя объекта" для вывода в заголовок редактора или крошки.
     * Формат:
     * экранное_имя_класса "имя_объекта_из_поля_с_именем"
     */
    public static function getFullObjectTitle($container_obj)
    {
        $container_obj_title = \Skif\CRUD\CRUDUtils::getModelTitleForObj($container_obj);
        $container_obj_model_screen_name = \Skif\CRUD\CRUDUtils::getModelClassScreenNameForObj($container_obj);

        return $container_obj_model_screen_name . ' "' . $container_obj_title . '"';
    }

    public static function getObjContainerObj($obj)
    {
        \Skif\Utils::assert($obj);

        $obj_class_name = get_class($obj);

        if (!property_exists($obj_class_name, 'crud_container_model')) {
            return null;
        }

        $container_model_arr = $obj_class_name::$crud_container_model;
        foreach ($container_model_arr as $container_model_class_name => $container_model_link_field_name) {
            // переделать - модель не обязана поддерживать activerecord
            $container_model_id = $obj->getFieldValueByName($container_model_link_field_name); // потому что свойство может быть защищенным и не доступным напрямую

            $container_obj = new $container_model_class_name;
            $container_is_loaded = $container_obj->load($container_model_id);
            \Skif\Utils::assert($container_is_loaded);

            return $container_obj;
        }

        throw new \Exception();
    }

    public static function getContainerObjByLinkFieldName($obj, $field_name)
    {
        \Skif\Utils::assert($obj);

        $obj_class_name = get_class($obj);

        if (!property_exists($obj_class_name, 'crud_container_model')) {
            return null;
        }

        $container_model_arr = $obj_class_name::$crud_container_model;
        foreach ($container_model_arr as $container_model_class_name => $container_model_link_field_name) {
            if ($container_model_link_field_name == $field_name) {
                return $container_model_class_name;
            }
        }

        return null;
    }

    /**
     * Кнопку "добавить" по умолчанию не выводим. Это для защиты, чтобы не создавали модели без родителей (или без других обязательных данных).
     * Кнопка включается наличием в модели поля crud_create_button_required_fields_arr.
     * Если в этом поле пустой массив - кнопка выводится всегда.
     * Если в этом поле массив имен полей - кнопка выводится, только если все поля из этого массива присутствуют в контенксте.
     * Т.е. в этот массив нужно включать поле с идентификатором родителя и т.п.
     *
     * @param $model_class_name
     * @param $context_arr
     * @return bool
     */
    public static function canDisplayCreateButton($model_class_name, $context_arr)
    {
        if (!property_exists($model_class_name, 'crud_create_button_required_fields_arr')) {
            return false;
        }

        $create_button_required_fields_arr = $model_class_name::$crud_create_button_required_fields_arr;

        if (!is_array($create_button_required_fields_arr)) {
            return false;
        }

        foreach ($create_button_required_fields_arr as $field) {
            if ((!array_key_exists($field, $context_arr)) || (!$context_arr[$field])) {
                return false;
            }
        }

        return true;
    }

    public static function exceptionIfClassNotImplementsInterface($class_name, $interface_class_name)
    {
        \Skif\Model\Helper::exceptionIfClassNotImplementsInterface($class_name, $interface_class_name);
    }

    public static function getModelTitle($model_class_name, $obj_id)
    {
        if (!property_exists($model_class_name, 'crud_model_title_field')) {
            return $model_class_name;
        }

        $obj = new $model_class_name;
        $obj->load($obj_id);

        return self::getModelTitleForObj($obj);
    }

    public static function getModelTitleForObj($obj)
    {
        \Skif\Utils::assert($obj);

        $obj_class_name = get_class($obj);

        if (!property_exists($obj_class_name, 'crud_model_title_field')) {
            return $obj_class_name;
        }

        $title_field = $obj_class_name::$crud_model_title_field;

        return self::getObjectFieldValue($obj, $title_field);
    }

    public static function getModelClassScreenNameForObj($obj)
    {
        \Skif\Utils::assert($obj);

        $obj_class_name = get_class($obj);

        if (!property_exists($obj_class_name, 'crud_model_class_screen_name')) {
            return $obj_class_name;
        }

        $crud_model_class_screen_name = $obj_class_name::$crud_model_class_screen_name;

        return $crud_model_class_screen_name;
    }

    public static function getObjectFieldValue($obj, $field_name)
    {
        $obj_class_name = get_class($obj);

        $reflect = new \ReflectionClass($obj_class_name);
        $field_prop_obj = null;

        foreach ($reflect->getProperties() as $prop_obj) {
            if ($prop_obj->getName() == $field_name) {
                $field_prop_obj = $prop_obj;
            }
        }

        \Skif\Utils::assert($field_prop_obj);

        $field_prop_obj->setAccessible(true);
        return $field_prop_obj->getValue($obj);
    }

    public static function getCrudEditorFieldsArrForClass($model_class_name)
    {
        $rc = new \ReflectionClass($model_class_name);

        if ($rc->hasMethod('crud_editorFieldsArr')) {
            return $model_class_name::crud_editorFieldsArr();
        }

        if (property_exists($model_class_name, 'crud_editor_fields_arr')) {
            return $model_class_name::$crud_editor_fields_arr;
        }


        return null;
    }

    public static function getCrudEditorFieldsArrForObj($obj)
    {
        return self::getCrudEditorFieldsArrForClass(get_class($obj));
    }

    public static function isRequiredField($model_class_name, $field_name)
    {
        $required = '';

        $crud_editor_fields_arr = self::getCrudEditorFieldsArrForClass($model_class_name);
        if ($crud_editor_fields_arr) {
            if ((array_key_exists($field_name, $crud_editor_fields_arr)) && (array_key_exists('required', $crud_editor_fields_arr[$field_name]))) {
                $required = $crud_editor_fields_arr[$field_name]['required'];
            }
        }

        return $required;
    }

    public static function getTitleForField($model_class_name, $field_name)
    {
        $title = $field_name;

        if (property_exists($model_class_name, 'crud_field_titles_arr')) {
            $crud_field_titles_arr = $model_class_name::$crud_field_titles_arr;
            if (array_key_exists($field_name, $crud_field_titles_arr)) {
                $title = $crud_field_titles_arr[$field_name];
            }
        }

        return $title;
    }

    public static function getDescriptionForField($model_class_name, $field_name)
    {
        $description = '';

        $crud_editor_fields_arr = self::getCrudEditorFieldsArrForClass($model_class_name);
        if ($crud_editor_fields_arr) {
            if ((array_key_exists($field_name, $crud_editor_fields_arr)) && (array_key_exists('description', $crud_editor_fields_arr[$field_name]))) {
                $description = $crud_editor_fields_arr[$field_name]['description'];
            }
        }

        return $description;
    }

    /**
     * Возвращает одну страницу списка объектов указанного класса.
     * Фильтры: массив $context_arr.
     * Как определяется страница: см. Pager.
     * @param $model_class_name - Имя класса модели
     * @param $context_arr - Массив пар "имя поля" - "значение поля"
     * @return array - Массив идентикаторов объектов.
     */
    public static function getObjIdsArrayForModel($model_class_name, $context_arr, $title_filter = '')
    {
        $page_size = \Skif\Pager::getPageSize();
        $start = \Skif\Pager::getPageOffset();

        $db_table_name = $model_class_name::DB_TABLE_NAME;

        $db_id = \Skif\Constants::DB_NAME_NEWS;
        if ($model_class_name::DB_ID) {
            $db_id = $model_class_name::DB_ID;
        }

        $db_id_field_name = self::getIdFieldName($model_class_name);

        $query_param_values_arr = array();

        $where = ' 1 = 1 ';
        foreach ($context_arr as $column_name => $value) {
            // чистим имя поля, возможно пришедшее из запроса
            $column_name = preg_replace("/[^a-zA-Z0-9_]+/", "", $column_name);

            $where .= ' and ' . $column_name . ' = ?';
            $query_param_values_arr[] = $value;
        }

        if (isset($model_class_name::$crud_model_title_field)) {
            $title_field_name = $model_class_name::$crud_model_title_field;
            if ($title_filter != '') {
                $where .= ' and ' . $title_field_name . ' like ?';
                $query_param_values_arr[] = '%' . $title_filter . '%';
            }
        }

        $order_field_name = $db_id_field_name;

        $objs_ids_arr = \Skif\DB\DBWrapper::readColumn(
            "SELECT $db_id_field_name FROM " . $db_table_name . "
                WHERE " . $where . "
                ORDER BY " . $order_field_name . " DESC
                LIMIT " . intval($page_size) . " OFFSET " . intval($start),
            $query_param_values_arr);

        return $objs_ids_arr;

    }

    public static function currentUserHasRightsToEditModel($model_class_name)
    {
        /*
        if (property_exists($model_class_name, 'operator_permissions_arr_required_to_edit')) {
        }
        */

        if (\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            return true;
        }

        return false;
    }

    public static function setObjectFieldsFromArray($obj, $values_arr)
    {
        $reflect = new \ReflectionClass($obj);

        foreach ($values_arr as $key => $value) {
            $property_obj = $reflect->getProperty($key);
            $property_obj->setAccessible(true);
            $property_obj->setValue($obj, $value);
        }

        return $obj;
    }

    public static function createAndLoadObject($model_class_name, $obj_id)
    {
        self::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceLoad');

        $obj = new $model_class_name;
        \Skif\Utils::assert($obj->load($obj_id));

        return $obj;
    }

    public static function getIdFieldName($model_class_name)
    {
        if (defined($model_class_name . '::DB_ID_FIELD_NAME')) {
            return $model_class_name::DB_ID_FIELD_NAME;
        }

        return 'id';
    }

    public static function stringCanBeUsedAsLinkText($text)
    {
        return preg_match('/[0-9A-Za-zА-Яа-яЁё]/u', $text);
    }
}