<?php

namespace Skif\Util;


class ActiveRecordHelper
{
    public static function getIdFieldName($model_obj)
    {
        $obj_class_name = get_class($model_obj);

        if (defined($obj_class_name . '::DB_ID_FIELD_NAME')) {
            $id_field_name = $obj_class_name::DB_ID_FIELD_NAME;
        } else {
            $id_field_name = 'id';
        }
        return $id_field_name;
    }

    /**
     * Сохранение записи
     * @param $model_obj
     */
    public static function saveModelObj($model_obj)
    {
        self::exceptionIfObjectIsIncompatibleWithActiveRecord($model_obj);

        $model_class_name = get_class($model_obj);
        $db_table_name = $model_class_name::DB_TABLE_NAME;
        $db_id_field_name = self::getIdFieldName($model_obj);

        $fields_to_save_arr = array();

        $reflect = new \ReflectionClass($model_obj);

	    $ignore_properties_names_arr = array();
	    if ($reflect->hasProperty('active_record_ignore_fields_arr'))
	    {
		    $ignore_properties_names_arr = $reflect->getProperty('active_record_ignore_fields_arr')->getValue();
	    }

        foreach ($reflect->getProperties() as $property_obj) {
            if (
                $property_obj->isStatic()
                || in_array($property_obj->getName(), $ignore_properties_names_arr)
            ) {
                continue; // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
	                        // также игнорируем свойства класса перечисленные в игнор листе $active_record_ignore_fields_arr
            }
            $property_obj->setAccessible(true);
            $fields_to_save_arr[$property_obj->getName()] = $property_obj->getValue($model_obj);
        }

        unset($fields_to_save_arr[$db_id_field_name]);

        $property_obj = $reflect->getProperty($db_id_field_name);
        $property_obj->setAccessible(true);
        $model_id_value = $property_obj->getValue($model_obj);

        if ($model_id_value == '') {
            $placeholders_arr = array_fill(0, count($fields_to_save_arr), '?');

            \Skif\DB\DBWrapper::query(
                'insert into ' . $db_table_name . ' (' . implode(',', array_keys($fields_to_save_arr)) . ') values (' . implode(',', $placeholders_arr) . ')',
                array_values($fields_to_save_arr)
            );

            $last_insert_id = \Skif\DB\DBWrapper::lastInsertId();
            $property_obj->setValue($model_obj, $last_insert_id);

        } else {
            $placeholders_arr = array();

            foreach ($fields_to_save_arr as $field_name => $field_value) {
                $placeholders_arr[] = $field_name . '=?';
            }

            $values_arr = array_values($fields_to_save_arr);
            array_push($values_arr, $model_id_value);

            $query = 'update ' . $db_table_name . ' set ' . implode(',', $placeholders_arr) . ' where ' . $db_id_field_name . ' = ?';
            \Skif\DB\DBWrapper::query($query, $values_arr);
        }

    }

    /**
     * Загружаем запись
     * @param $model_obj
     * @param $id
     * @return bool
     */
    public static function loadModelObj($model_obj, $id)
    {
        self::exceptionIfObjectIsIncompatibleWithActiveRecord($model_obj);

        $model_class_name = get_class($model_obj);
        $db_table_name = $model_class_name::DB_TABLE_NAME;
        $db_id_field_name = self::getIdFieldName($model_obj);

        $data_obj = \Skif\DB\DBWrapper::readObject(
            'select * from ' . $db_table_name . ' where ' . $db_id_field_name . ' = ?',
            array($id)
        );

        if (!$data_obj) {
            return false;
        }

        $reflect = new \ReflectionClass($model_class_name);
        foreach ($data_obj as $field_name => $field_value) {
            $property = $reflect->getProperty($field_name);
            $property->setAccessible(true);
            $property->setValue($model_obj, $field_value);
        }

        return true;
    }

    public static function getIdByFieldNamesArr($model_obj, $field_names_arr)
    {
        $model_class_name = get_class($model_obj);
        $db_table_name = $model_class_name::DB_TABLE_NAME;
        $db_id_field_name = self::getIdFieldName($model_obj);

        $query = "SELECT " . $db_id_field_name . " FROM " . $db_table_name . " WHERE";

        $queries_arr = array();
        $param_arr = array();

        foreach ($field_names_arr as $field_name => $field_value) {
            $queries_arr[] = $field_name . "=?";
            $param_arr[] = $field_value;
        }

        $query .= " " . implode (' AND ', $queries_arr);

        $id = \Skif\DB\DBWrapper::readField(
            $query,
            $param_arr
        );

        return $id;
    }


    /**
     * Удаление записи
     * @param $model_obj
     * @return \PDOStatement
     */
    public static function deleteModelObj($model_obj)
    {
        $model_class_name = get_class($model_obj);
        $db_table_name = $model_class_name::DB_TABLE_NAME;
        $db_id_field_name = self::getIdFieldName($model_obj);

        self::exceptionIfObjectIsIncompatibleWithActiveRecord($model_obj);

        $reflect = new \ReflectionClass($model_obj);
        $property_obj = $reflect->getProperty($db_id_field_name);
        $property_obj->setAccessible(true);
        $model_id_value = $property_obj->getValue($model_obj);

        $result = \Skif\DB\DBWrapper::query(
            'DELETE FROM ' . $db_table_name . ' where ' . $db_id_field_name . ' = ?',
            array($model_id_value)
        );

        return $result;
    }

    /**
     * Проверяет, что объект (класс его) предоставляет нужные константы и т.п.
     * Если что-то не так - выбрасывает исключение. По исключениям разработчик класса может понять чего не хватает.
     * @param $obj
     * @throws \Exception
     */
    static public function exceptionIfObjectIsIncompatibleWithActiveRecord($obj)
    {
        if (!is_object($obj)) {
            throw new \Exception('must be object');
        }

        $obj_class_name = get_class($obj);

        self::exceptionIfClassIsIncompatibleWithActiveRecord($obj_class_name);
    }

    static public function exceptionIfClassIsIncompatibleWithActiveRecord($class_name)
    {
        if (!defined($class_name . '::DB_TABLE_NAME')) {
            throw new \Exception('class must provide DB_TABLE_NAME constant to use ActiveRecord');
        }
    }
}