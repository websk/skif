<?php

namespace WebSK\Skif\CRUD\UserWidget;


class UserWidget
{
    public static function renderWidget($field_name, $field_value, $widget_options = array())
    {
        $widget_options['field_name'] = $field_name;
        $widget_options['field_value'] = $field_value;

        $output = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'CRUD' . DIRECTORY_SEPARATOR . 'UserWidget',
            'user_widget.tpl.php',
            $widget_options
        );

        return $output;
    }

}