<?php

namespace Skif\Util;

/**
 * Для работы с ActiveRecord необходимо:
 *
 * 1. создаем таблицу в БД с полем "id" (auto increment) и прочими нужными полями
 * 2. создаем класс для модели:
 *      - для каждого поля в таблице у класса должно быть свое свойство
 *      - значения по-умолчанию должны соответствовать полям таблицы
 *      - указываемм две константы:
 *          - const DB_ID           - идентификатор БД (news, stats, etc.)
 *          - const DB_TABLE_NAME   - имя таблицы в которой хранятся данные модели
 *      - подключаем трейты:
 *          - ProtectProperties
 *          - ActiveRecord
 *      - пишем необходимые геттеры и сеттеры
 *
 * Сделано трейтом, чтобы:
 * - был нормальный доступ к данным объекта (в т.ч. защищенным)
 * - идешка видела методы ActiveRecord
 * Class ActiveRecord
 * @package Skif\Util
 */
trait ActiveRecord
{
    /**
     * пока работаем с полями объекта напрямую, без сеттеров/геттеров
     * этот метод позволяет писать в защищенные свойства (используется, например, в CRUD)
     * @param $fields_arr
     */
    public function ar_setFields($fields_arr)
    {
        foreach ($fields_arr as $field_name => $field_value) {
            $this->$field_name = $field_value;
        }
    }

    public function getFieldValueByName($field_name){
        return $this->$field_name;
    }

    public function save()
    {
        \Skif\Util\ActiveRecordHelper::saveModelObj($this);

        if (
            ($this instanceof \Skif\Model\InterfaceLoad) &&
            ($this instanceof \Skif\Model\InterfaceFactory)
        ) {
            $this::afterUpdate($this->getId());

            if ($this instanceof \Skif\Model\InterfaceLogger) {
                \Skif\Logger\Logger::logObjectEvent($this, 'изменение');
            }
        }
    }

    public function getIdByFieldNamesArr($field_names_arr) {
        return \Skif\Util\ActiveRecordHelper::getIdByFieldNamesArr($this, $field_names_arr);
    }

    public function load($id)
    {
        return \Skif\Util\ActiveRecordHelper::loadModelObj($this, $id);
    }

    public function delete()
    {
        if (
            ($this instanceof \Skif\Model\InterfaceLoad) &&
            ($this instanceof \Skif\Model\InterfaceFactory)
        ) {
            $check_message = $this->BeforeDelete();
            if ($check_message !== true) {
                return $check_message;
            }

            \Skif\Util\ActiveRecordHelper::deleteModelObj($this);
            $this->afterDelete();
        } else {
            \Skif\Util\ActiveRecordHelper::deleteModelObj($this);
        }

        if ($this instanceof \Skif\Model\InterfaceLogger) {
            \Skif\Logger\Logger::logObjectEvent($this, 'удаление');
        }

        return true;
    }
}
