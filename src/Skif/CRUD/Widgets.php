<?php

namespace Skif\CRUD;


class Widgets
{

    public static function renderFieldWithWidget($field_name, $obj, $field_value = '')
    {
        $widget_name = self::getFieldWidgetName($field_name, $obj);

        if (!$field_value) {
            $field_value = \Skif\CRUD\CRUDUtils::getObjectFieldValue($obj, $field_name);
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
                $o = self::widgetTextArea($field_name, $field_value);
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

    public static function renderListFieldWithWidget($field_name, $obj, $field_value = '')
    {
        $widget_name = self::getListWidgetName($field_name, $obj);

        if (!$field_value) {
            $field_value = \Skif\CRUD\CRUDUtils::getObjectFieldValue($obj, $field_name);
        }

        if ($widget_name) {
            \Skif\Utils::assert(is_callable($widget_name));
            $widget_options = self::getWidgetSettings($field_name, $obj);

            return call_user_func_array($widget_name, array($field_name, $field_value, $widget_options));
        }

        return $field_value;
    }

    public static function getFieldWidgetName($field_name, $obj)
    {
        $crud_editor_fields_arr = \Skif\CRUD\CRUDUtils::getCrudEditorFieldsArrForObj($obj);

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

    public static function getListWidgetName($field_name, $obj)
    {
        $crud_editor_fields_arr = \Skif\CRUD\CRUDUtils::getCrudEditorFieldsArrForObj($obj);

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

    public static function getWidgetSettings($field_name, $obj)
    {
        $crud_editor_fields_arr = \Skif\CRUD\CRUDUtils::getCrudEditorFieldsArrForObj($obj);

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

    public static function getFieldWidgetOptionsArr($field_name, $obj)
    {
        $crud_editor_fields_arr = \Skif\CRUD\CRUDUtils::getCrudEditorFieldsArrForObj($obj);

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

    public static function widgetGetNodeIdByUrl($field_name, $field_value)
    {
        $html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'CRUD',
            'content_id.tpl.php',
            array(
                'field_name' => $field_name,
                'field_value' => $field_value
            )
        );

        return $html;
    }

    /*
    public static function widgetGetNodeIdByUrlAjax()
    {
        if (array_key_exists('node-url', $_POST)) {
            if ($_POST['node-url'] != '') {
                if (!preg_match('/^[0-9]+$/', $_POST['node-url'])) {
                    $url = str_replace(\Skif\Conf\Common::get()['news_domain'], "", $_POST['node-url']);
                    $parts = explode('?', $url);
                    $node_id = \Skif\Content\ContentUtils::getContentIdByUrl($parts[0]);
                } else {
                    $node_id = $_POST['node-url'];
                }

                $node_obj = \Skif\Node\NodeFactory::loadNode($node_id);

                $node_title = $node_obj->getTitle();
                if (!\Skif\CRUD\CRUDUtils::stringCanBeUsedAsLinkText($node_title)) {
                    $node_title = $node_id;
                }

                $json = array("node_id" => $node_id, "node_title" => $node_title);
                $html = json_encode($json);
            }
        } else {
            $html = \Skif\PhpTemplate::renderTemplateBySkifModule(
                'CRUD',
                'node_id_form.tpl.php'
            );
        }

        echo $html;
    }
    */

    public static function widgetInput($field_name, $field_value, $obj)
    {
        $widget_options = self::getWidgetSettings($field_name, $obj);

        return '<input id="' . $field_name . '"name="' . $field_name . '" value="' . htmlspecialchars($field_value) . '" class="form-control"'
            . (($obj->getId() && array_key_exists('disabled', $widget_options)) ? ' disabled' : '') . '>';
    }

    public static function widgetTextArea($field_name, $field_value)
    {

        return '<textarea name="' . $field_name . '" class="form-control" rows="10">' . htmlspecialchars($field_value) . '</textarea>';
    }

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

    public static function widgetOptions($field_name, $field_value, $options_arr)
    {
        $options = '<option></option>';

        foreach ($options_arr as $value => $title) {
            $selected_html_attr = '';
            if (($field_value!= '') && ($field_value == $value)) {
                $selected_html_attr = ' selected';
            }

            $options .= '<option value="' . $value . '"' . $selected_html_attr . '>' . $title . '</option>';
        }

        return '<select name="' . $field_name . '" class="form-control">' . $options . '</select>';
    }

}