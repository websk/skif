<?php
/**
 * Created by PhpStorm.
 * User: kdobryansky
 * Date: 21.03.14
 * Time: 14:59
 */

namespace Skif\CRUD;


class Widgets {

    public static function renderFieldWithWidget($field_name, $obj, $field_value = '')
    {

        $widget_name = self::getFieldWidgetName($field_name, $obj);

        if(!$field_value){
            $field_value = \Skif\CRUD\Helpers::getObjectFieldValue($obj, $field_name);
        }

        if (is_callable($widget_name)){

            $widget_options = self::getWidgetSettings($field_name, $obj);

            return call_user_func_array($widget_name, array($field_name, $field_value, $widget_options));
        }

        switch($widget_name){
            case 'get_node_id_by_url':
                $o = self::widgetGetNodeIdByUrl($field_name, $field_value);
                break;
            case 'get_media_id_by_url':
                $o = self::widgetGetMediaIdByUrl($field_name, $field_value);
                break;
            case 'select_game':
                $o = self::widgetSelectGameDatepicker($field_name, $field_value);
                break;
            case 'checkbox':
                $o = self::widgetCheckbox($field_name, $field_value);
                break;
            case 'options':
                $options_arr = self::getFieldWidgetOptionsArr($field_name, $obj);
                $o = self::widgetOptions($field_name, $field_value, $options_arr);
                break;
            case 'term_reference':
                $o = self::widgetTermReference($field_name, $field_value);
                break;
            default:
                $o = self::widgetInput($field_name, $field_value);
        }

        return $o;

    }

    public static function renderListFieldWithWidget($field_name, $obj, $field_value = '')
    {
        $widget_name = self::getListWidgetName($field_name, $obj);

        if (!$field_value) {
            $field_value = \Skif\CRUD\Helpers::getObjectFieldValue($obj, $field_name);
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
        $crud_editor_fields_arr = \Skif\CRUD\Helpers::getCrudEditorFieldsArrForObj($obj);

        if(!$crud_editor_fields_arr){
            return '';
        }

        if(!array_key_exists($field_name, $crud_editor_fields_arr)){
            return '';
        }

        if(!array_key_exists('widget', $crud_editor_fields_arr[$field_name])){
            return '';
        }

        return $crud_editor_fields_arr[$field_name]['widget'];

    }

    public static function getListWidgetName($field_name, $obj)
    {
        $crud_editor_fields_arr = \Skif\CRUD\Helpers::getCrudEditorFieldsArrForObj($obj);

        if(!$crud_editor_fields_arr){
            return '';
        }

        if(!array_key_exists($field_name, $crud_editor_fields_arr)){
            return '';
        }

        if(!array_key_exists('list_widget', $crud_editor_fields_arr[$field_name])){
            return '';
        }

        return $crud_editor_fields_arr[$field_name]['list_widget'];

    }

    public static function getWidgetSettings($field_name, $obj)
    {
        $crud_editor_fields_arr = \Skif\CRUD\Helpers::getCrudEditorFieldsArrForObj($obj);

        if(!$crud_editor_fields_arr){
            return array();
        }

        if(!array_key_exists($field_name, $crud_editor_fields_arr)){
            return array();
        }

        if(!array_key_exists('widget_settings', $crud_editor_fields_arr[$field_name])){
            return array();
        }

        return $crud_editor_fields_arr[$field_name]['widget_settings'];

    }

    public static function getFieldWidgetOptionsArr($field_name, $obj)
    {
        $crud_editor_fields_arr = \Skif\CRUD\Helpers::getCrudEditorFieldsArrForObj($obj);

        if (!$crud_editor_fields_arr){
            return array();
        }

        if (!array_key_exists($field_name, $crud_editor_fields_arr)){
            return array();
        }

        if (!array_key_exists('options_arr', $crud_editor_fields_arr[$field_name])){
            return array();
        }

        return $crud_editor_fields_arr[$field_name]['options_arr'];
    }

    public static function widgetGetNodeIdByUrl($field_name, $field_value)
    {
		$html = \Skif\Render::template2('Skif/CRUD/templates/node_id.tpl.php', array(
                'field_name' => $field_name,
                'field_value' => $field_value
            )
        );

        return $html;
    }

    public static function widgetGetMediaIdByUrl($field_name, $field_value)
    {
        $html = \Skif\Render::template2('Skif/CRUD/templates/media_id.tpl.php', array(
                'field_name' => $field_name,
                'field_value' => $field_value
            )
        );

        return $html;
    }

    public static function widgetGetNodeIdByUrlAjax()
    {
		if (array_key_exists('node-url', $_POST)) {
			if ($_POST['node-url'] != '') {
				if (!preg_match('/^[0-9]+$/',$_POST['node-url'])) {
					$url = str_replace(\Skif\Conf\Common::get()['news_domain'], "", $_POST['node-url']);
					$parts = explode('?', $url);
					$node_id = \Skif\Node\NodeHelper::getNodeIdByUrl($parts[0]);
				}
				else {
					$node_id = $_POST['node-url'];
				}

                $node_obj = \Skif\Node\NodeFactory::loadNode($node_id);

                $node_title = $node_obj->getTitle();
                if (!\Skif\CRUD\Helpers::stringCanBeUsedAsLinkText($node_title)) {
                    $node_title = $node_id;
                }

                $json = array("node_id" => $node_id, "node_title" => $node_title);
				$html = json_encode($json);
			}
		}
		else {
			$html = \Skif\Render::template2('Skif/CRUD/templates/node_id_form.tpl.php');
		}
		
		echo $html;
    }

    public static function widgetSelectGameDatepicker($field_name, $field_value)
    {
		$html = \Skif\Render::template2('Skif/CRUD/templates/game_id.tpl.php', array(
                'field_name' => $field_name,
                'field_value' => $field_value
            )
        );

        return $html;
    }

    public static function widgetSelectGameDatepickerAjax()
    {
		$html = \Skif\Render::template2('Skif/CRUD/templates/game_id_form.tpl.php');

        echo $html;
    }

    public static function widgetInput($field_name, $field_value)
    {

        //return '<input class="form-control" id="' . $field_name . '"
        //                       name="' . $field_name . '"
        //                       value="' . $field_value . '">';

        return '<textarea name="' . $field_name . '" class="form-control" rows="1">' . $field_value . '</textarea>';

    }

    public static function widgetCheckbox($field_name, $field_value)
    {

        $checked_str = '';

        if($field_value){
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

        foreach($options_arr as $value => $title)
        {
            $selected_html_attr = '';
            if ($field_value == $value) {
                $selected_html_attr = ' selected';
            }

            $options .= '<option value="' .  $value . '"' . $selected_html_attr . '>' . $title . '</option>';
        }

        return '<select name="' . $field_name . '" class="form-control">' . $options . '</select>';
    }

    public static function widgetTermReference($field_name, $field_value)
    {

        $top_tids_arr = \Skif\Term\TermHelper::getVocabularyRootTermsIdsArr(\Skif\Constants::VID_MAIN_RUBRICS);

        $top_tids_arr = \Skif\CRUD\WidgetHelpers::sortTermIdsArrByTitle($top_tids_arr);

        $option = '';

        foreach($top_tids_arr as $top_tid){

            $term_obj = \Skif\Term\TermFactory::getTermObj($top_tid);
            \Skif\Utils::assert($term_obj);

            $selected_html_attr = '';
            if($term_obj->getId() == $field_value){
                $selected_html_attr = ' selected';
            }

            $option .= '<option value="' . $term_obj->getId() . '"' . $selected_html_attr . '>' . $term_obj->getTitle() . '</option>';
            $option .= self::widgetTermOptionsRecursion($term_obj, $field_value);

        }

        return '<select name="' . $field_name . '" class="form-control">' . $option . '</select>';

    }

    public static function widgetTermOptionsRecursion($parent_term_obj, $current_selected_tid)
    {

        $children_ids_arr = $parent_term_obj->getChildrenIdsArr($parent_term_obj->getId());

        if(!$children_ids_arr){
            return '';
        }

        $children_ids_sorted_arr = \Skif\CRUD\WidgetHelpers::sortTermIdsArrByTitle($children_ids_arr);

        $o = '';

        foreach($children_ids_sorted_arr as $child_tid)
        {

            $child_term_obj = \Skif\Term\TermFactory::getTermObj($child_tid);
            \Skif\Utils::assert($child_term_obj);

            $parents_path_str = \Skif\CRUD\WidgetHelpers::getParentsPathStringForTid($child_tid);

            $selected_html_attr = '';
            if($child_tid == $current_selected_tid){
                $selected_html_attr = ' selected';
            }

            $o .= '<option value="' .  $child_tid . '"' . $selected_html_attr . '>' . $parents_path_str . '</option>';

            $o .= self::widgetTermOptionsRecursion($child_term_obj, $current_selected_tid);

        }

        return $o;

    }

}