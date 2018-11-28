<?php

namespace WebSK\Skif\Form;


class FormFieldController extends \WebSK\Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\Form\FormField';

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/form_field';
    }

}