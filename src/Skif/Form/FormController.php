<?php

namespace Skif\Form;


class FormController extends \Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\Form\Form';

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/form';
    }


}