<?php

namespace WebSK\Skif\Form;

use WebSK\Skif\CRUD\CRUDController;

/**
 * Class FormFieldController
 * @package WebSK\Skif\Form
 */
class FormFieldController extends CRUDController
{
    /** @var string */
    protected static $model_class_name = FormField::class;

    /**
     * @param string $model_class_name
     * @return string
     */
    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/form_field';
    }
}
