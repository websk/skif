<?php
namespace Skif\CRUD\CKEditorWidget;

class CKEditorWidget {

    public static function renderWidget($field_name, $field_value, $widget_options = array())
    {
        $dir = null;
        if (array_key_exists('dir', $widget_options)) {
            $dir = $widget_options['dir'];
        }

        $height = 300;
        if (array_key_exists('height', $widget_options)) {
            $height = $widget_options['height'];
        }

        $type = \Skif\CKEditor\CKEditor::CKEDITOR_BASIC;
        if (array_key_exists('type', $widget_options)) {
            $type = $widget_options['type'];
        }

        if ($type == \Skif\CKEditor\CKEditor::CKEDITOR_FULL) {
            $output = \Skif\CKEditor\CKEditor::createFullCKEditor($field_name, $field_value, $height, $dir);
        } else {
            $output = \Skif\CKEditor\CKEditor::createBasicCKEditor($field_name, $field_value, $height, $dir);
        }

        return $output;
    }

}