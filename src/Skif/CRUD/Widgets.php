<?php

namespace Skif\CRUD;

use Skif\PhpTemplate;
use Skif\Utils;

class Widgets
{

    /**
     * @param string $field_name
     * @param $obj
     * @param string $field_value
     * @return mixed|string
     */
    public static function renderFieldWithWidget($field_name, $obj, $field_value = '')
    {
        $widget_name = self::getFieldWidgetName($field_name, $obj);

        if (!$field_value) {
            $field_value = CRUDUtils::getObjectFieldValue($obj, $field_name);
        }

        $widget_options = self::getWidgetSettings($field_name, $obj);

        if (is_callable($widget_name)) {
            return call_user_func_array($widget_name, array($field_name, $field_value, $widget_options));
        }

        switch ($widget_name) {
            case 'get_content_id_by_url':
                $o = self::widgetGetNodeIdByUrl($field_name, $field_value);
                break;
            case 'textarea':
                $o = self::widgetTextArea($field_name, $field_value, $obj);
                break;
            case 'checkbox':
                $o = self::widgetCheckbox($field_name, $field_value);
                break;
            case 'options':
                $options_arr = self::getFieldWidgetOptionsArr($field_name, $obj);
                $o = self::widgetOptions($field_name, $field_value, $options_arr);
                break;
            default:
                $o = self::widgetInput($field_name, $field_value, $obj);
        }

        return $o;

    }

    /**
     * @param string $field_name
     * @param $obj
     * @param $field_value
     * @return mixed|string
     */
    public static function renderListFieldWithWidget($field_name, $obj, $field_value = '')
    {
        $widget_name = self::getListWidgetName($field_name, $obj);

        if (!$field_value) {
            $field_value = CRUDUtils::getObjectFieldValue($obj, $field_name);
        }

        if ($widget_name) {
            Utils::assert(is_callable($widget_name));
            $widget_options = self::getWidgetSettings($field_name, $obj);

            return call_user_func_array($widget_name, array($field_name, $field_value, $widget_options));
        }

        return $field_value;
    }

    /**
     * @param string $field_name
     * @param $obj
     * @return string
     */
    public static function getFieldWidgetName($field_name, $obj)
    {
        $crud_editor_fields_arr = CRUDUtils::getCrudEditorFieldsArrForObj($obj);

        if (!$crud_editor_fields_arr) {
            return '';
        }

        if (!array_key_exists($field_name, $crud_editor_fields_arr)) {
            return '';
        }

        if (!array_key_exists('widget', $crud_editor_fields_arr[$field_name])) {
            return '';
        }

        return $crud_editor_fields_arr[$field_name]['widget'];
    }

    /**
     * @param string $field_name
     * @param $obj
     * @return string
     */
    public static function getListWidgetName($field_name, $obj)
    {
        $crud_editor_fields_arr = CRUDUtils::getCrudEditorFieldsArrForObj($obj);

        if (!$crud_editor_fields_arr) {
            return '';
        }

        if (!array_key_exists($field_name, $crud_editor_fields_arr)) {
            return '';
        }

        if (!array_key_exists('list_widget', $crud_editor_fields_arr[$field_name])) {
            return '';
        }

        return $crud_editor_fields_arr[$field_name]['list_widget'];
    }

    /**
     * @param string $field_name
     * @param $obj
     * @return array
     */
    public static function getWidgetSettings($field_name, $obj)
    {
        $crud_editor_fields_arr = CRUDUtils::getCrudEditorFieldsArrForObj($obj);

        if (!$crud_editor_fields_arr) {
            return array();
        }

        if (!array_key_exists($field_name, $crud_editor_fields_arr)) {
            return array();
        }

        if (!array_key_exists('widget_settings', $crud_editor_fields_arr[$field_name])) {
            return array();
        }

        return $crud_editor_fields_arr[$field_name]['widget_settings'];
    }

    /**
     * @param string $field_name
     * @param $obj
     * @return array
     */
    public static function getFieldWidgetOptionsArr($field_name, $obj)
    {
        $crud_editor_fields_arr = CRUDUtils::getCrudEditorFieldsArrForObj($obj);

        if (!$crud_editor_fields_arr) {
            return array();
        }

        if (!array_key_exists($field_name, $crud_editor_fields_arr)) {
            return array();
        }

        if (!array_key_exists('options_arr', $crud_editor_fields_arr[$field_name])) {
            return array();
        }

        return $crud_editor_fields_arr[$field_name]['options_arr'];
    }

    /**
     * @param string $field_name
     * @param $field_value
     * @return string
     */
    public static function widgetGetNodeIdByUrl($field_name, $field_value)
    {
        $html = PhpTemplate::renderTemplateBySkifModule(
            'CRUD',
            'content_id.tpl.php',
            array(
                'field_name' => $field_name,
                'field_value' => $field_value
            )
        );

        return $html;
    }

    /**
     * @param string $field_name
     * @param $field_value
     * @param $obj
     * @return string
     */
    public static function widgetInput($field_name, $field_value, $obj)
    {
        $widget_options = self::getWidgetSettings($field_name, $obj);

        return '<input id="' . $field_name . '"name="' . $field_name . '" value="' . htmlspecialchars($field_value) . '" class="form-control"'
            . (($obj->getId() && array_key_exists('disabled', $widget_options)) ? ' disabled' : '') . '>';
    }

    /**
     * @param $field_name
     * @param $field_value
     * @param $obj
     * @return string
     */
    public static function widgetTextArea($field_name, $field_value, $obj)
    {
        $widget_options = self::getWidgetSettings($field_name, $obj);

        return '<textarea name="' . $field_name . '" class="form-control" rows="10"'
            . (($obj->getId() && array_key_exists('disabled', $widget_options)) ? ' disabled' : '') . '>' . htmlspecialchars($field_value) . '</textarea>';
    }

    /**
     * @param string $field_name
     * @param $field_value
     * @return string
     */
    public static function widgetCheckbox($field_name, $field_value)
    {
        $checked_str = '';

        if ($field_value) {
            $checked_str = ' checked';
        }

        // после будет скрыто и попадет в POST только в том случае, если checkbox будет unchecked
        $hidden_field_for_unchecked_state = '<input type="hidden" name="' . $field_name . '" value="0">';

        $visible_checkbox = '<input type="checkbox" id="' . $field_name . '"
                               name="' . $field_name . '"
                               value="1"
                               ' . $checked_str . '>';

        return $hidden_field_for_unchecked_state . $visible_checkbox;
    }

    /**
     * @param string $field_name
     * @param $field_value
     * @param $options_arr
     * @return string
     */
    public static function widgetOptions($field_name, $field_value, $options_arr)
    {
        $options = '<option></option>';

        foreach ($options_arr as $value => $title) {
            $selected_html_attr = '';
            if ($field_value == $value) {
                $selected_html_attr = ' selected';
            }

            $options .= '<option value="' . $value . '"' . $selected_html_attr . '>' . $title . '</option>';
        }

        return '<select name="' . $field_name . '" class="form-control">' . $options . '</select>';
    }
}