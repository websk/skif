<?php

namespace Skif\KeyValue;


class KeyValueController extends \Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\KeyValue\KeyValue';

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/key_value';
    }

    protected static function createValidation()
    {
        $name = $_POST['name'];

        if (!preg_match("/^[a-zA-Z0-9_-]+$/", $name)) {
            \Skif\Messages::setError('Неверное название переменной. Название должно состоять только из латинских букв <code>A-Za-z</code>, цифр <code>0-9</code>, тире <code>-</code> или подчёркивания <code>_</code>');

            return false;
        }

        return true;
    }

}

