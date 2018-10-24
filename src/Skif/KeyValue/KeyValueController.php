<?php

namespace Skif\KeyValue;

use Skif\CRUD\CRUDController;
use Websk\Skif\Messages;

class KeyValueController extends CRUDController
{

    /** @var string */
    protected static $model_class_name = KeyValue::class;

    /**
     * @param string $model_class_name
     * @return string
     */
    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/key_value';
    }

    /**
     * @return bool
     */
    protected static function createValidation()
    {
        $name = $_POST['name'];

        if (!preg_match("/^[a-zA-Z0-9_-]+$/", $name)) {
            Messages::setError('Неверное название параметра. Название должно состоять только из латинских букв <code>A-Za-z</code>, цифр <code>0-9</code>, тире <code>-</code> или подчёркивания <code>_</code>');

            return false;
        }

        return true;
    }
}
