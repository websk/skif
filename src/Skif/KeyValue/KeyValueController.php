<?php

namespace Skif\KeyValue;


class KeyValueController extends \Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\KeyValue\KeyValue';

    public static function getBaseUrl($model_class_name)
    {
        return '/admin/key_value';
    }

    public function createAction()
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $name = $_POST['name'];
        $value = $_POST['value'];
        $description = $_POST['description'];

        if (!preg_match("/^[a-zA-Z0-9_-]+$/", $name)) {
            \Skif\Messages::setError('Неверное название KeyValue. Название должно состоять только из латинских букв <code>A-Za-z</code>, цифр <code>0-9</code>, тире <code>-</code> или подчёркивания <code>_</code>');
            \Skif\Http::redirect('/admin/key_value/add/');
        }

        $key_value_obj = new \Skif\KeyValue\KeyValue();
        $key_value_obj->setName($name);
        $key_value_obj->setDescription($description);
        $key_value_obj->setValue($value);
        $key_value_obj->save();

        \Skif\Messages::setMessage('Изменения сохранены');

        \Skif\Http::redirect('/admin/key_value/edit/' . $key_value_obj->getId());
    }
}

