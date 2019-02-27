<?php

namespace WebSK\Skif\CRUD\UserWidget;

use WebSK\Views\PhpRender;

class UserWidget
{
    public static function renderWidget($field_name, $field_value, $widget_options = [])
    {
        $widget_options['field_name'] = $field_name;
        $widget_options['field_value'] = $field_value;

        $output = PhpRender::renderTemplateInViewsDir(
            'user_widget.tpl.php',
            $widget_options
        );

        return $output;
    }
}
