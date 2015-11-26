<?php

namespace Skif\KeyValue;


class KeyValueController extends \Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\KeyValue\KeyValue';


    /*
    public function listAction()
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'KeyValue',
            'key_values_list.tpl.php'
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'title' => "Переменные",
                'content' => $content
            )
        );
    }

    public function editAction($key_value_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        if (!$key_value_id) {
            \Skif\Http::exit404();
        }

        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'KeyValue',
            'key_value_form_edit.tpl.php',
            array(
                'key_value_id' => $key_value_id
            )
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'title' => ($key_value_id == 'new') ? 'Создание' : 'Редактирование',
                'content' => $content,
                'breadcrumbs_arr' => array('Переменные' => '/admin/key_value')
            )
        );
    }

    public function saveAction($key_value_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $name = $_POST['name'];
        $value = $_POST['value'];
        $description = $_POST['description'];

        if ($key_value_id == 'new') {
            if (!preg_match("/^[a-zA-Z0-9_-]+$/", $name)) {
                \Skif\Messages::setError('Неверное название KeyValue. Название должно состоять только из латинских букв <code>A-Za-z</code>, цифр <code>0-9</code>, тире <code>-</code> или подчёркивания <code>_</code>');
                \Skif\Http::redirect('/admin/key_value/edit/' . $key_value_id);
            }

            $key_value_obj = new \Skif\KeyValue\KeyValue();
            $key_value_obj->setName($name);
        } else {
            $key_value_obj = \Skif\KeyValue\KeyValue::factory($key_value_id);
        }

        $key_value_obj->setDescription($description);
        $key_value_obj->setValue($value);
        $key_value_obj->save();

        \Skif\Messages::setMessage('Изменения сохранены');

        \Skif\Http::redirect('/admin/key_value/edit/' . $key_value_obj->getId());
    }

    public function deleteAction($key_value_id)
    {
        // Проверка прав доступа
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        $key_value_obj = \Skif\KeyValue\KeyValue::factory($key_value_id);
        $key_value_obj->delete();

        \Skif\Messages::setMessage('Переменная удалена');

        \Skif\Http::redirect('/admin/key_value');
    }

    */
}

