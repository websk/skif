<?php

namespace Skif\Widgets\MultiSelectWidget;

class MultiSelectWidget
{
    public static function renderWidget($field_name, $field_value, $widget_options = array())
    {
        $widget_options['field_name'] = $field_name;
        $widget_options['field_value'] = $field_value;

        $output = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Widgets' . DIRECTORY_SEPARATOR . 'ImageUploadWidget',
            'image_upload_widget.tpl.php',
            $widget_options
        );

        return $output;
    }

}