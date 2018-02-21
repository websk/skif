<?php

namespace Skif\CRUD\DatepickerWidget;

use Skif\PhpTemplate;

class DatepickerWidget {

    public static function renderWidget($field_name, $field_value, $widget_options = array())
    {
        $widget_options['field_name'] = $field_name;
        $widget_options['field_value'] = $field_value;

        $output = PhpTemplate::renderTemplateBySkifModule(
            'CRUD' . DIRECTORY_SEPARATOR . 'DatepickerWidget',
            'datepicker_widget.tpl.php',
            $widget_options
        );

        return $output;
    }
}
