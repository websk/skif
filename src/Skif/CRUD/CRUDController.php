<?php

namespace Skif\CRUD;

/**
 * CRUD проверяет, реализует ли модель функционал моделей.
 * Если умеет загружаться - круд может показывать такие модели.
 * Если умеет сохраняться - круд может редактировать такие модели.
 */
class CRUDController extends \Skif\BaseController
{
    public static $base_breadcrumbs = array();
    protected static $model_class_name = '';
    protected static $controller_class_name = '';

    protected static function getLayoutTemplateFile()
    {
        return \Skif\Conf\ConfWrapper::value('layout.admin');
    }

    public static function getControllerClassNameByModelClassName($model_class_name)
    {
        if (static::$controller_class_name) {
            return static::$controller_class_name;
        }

        return $model_class_name . 'Controller';
        //return \Skif\UrlManager::$current_controller_obj;
    }

    public static function getModelClassName()
    {
        if (static::$model_class_name) {
            return static::$model_class_name;
        }

        $current_url_no_query = \Skif\UrlManager::getUriNoQueryString();

        if (preg_match('@^/crud/([\d\w\%]+)/(.+)@i', $current_url_no_query, $matches_arr)) {
            return urldecode($matches_arr[1]);
        }

        if (preg_match('@^/crud/([\d\w\%]+)@i', $current_url_no_query, $matches_arr)) {
            return urldecode($matches_arr[1]);
        }

        return null;
    }

    protected static function createValidation()
    {
        return true;
    }

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/crud/' . urlencode($model_class_name);
    }

    public static function getListUrl($model_class_name)
    {
        return static::getCRUDBaseUrl($model_class_name);
    }

    public static function getCreateUrl($model_class_name)
    {
        return static::getCRUDBaseUrl($model_class_name) . '/create';
    }

    public static function getAddUrl($model_class_name)
    {
        return static::getCRUDBaseUrl($model_class_name) . '/add';
    }

    /**
     * Генерирует ссылку на редактор объекта
     */
    public static function getEditUrl($model_class_name, $obj_id)
    {
        return static::getCRUDBaseUrl($model_class_name) . '/edit/' . $obj_id;
    }

    /**
     * Генерирует ссылку на редактор объекта
     */
    public static function getEditUrlForObj($obj)
    {
        // добавляем \ в начале имени класса - мы всегда работаем с классами в глобальном неймспейсе
        $obj_class_name = '\\' . get_class($obj);

        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($obj_class_name, 'Skif\Model\InterfaceLoad');

        $obj_id = $obj->getId();

        return static::getEditUrl($obj_class_name, $obj_id);
    }

    public static function getDeleteUrl($model_class_name, $obj_id)
    {
        return static::getCRUDBaseUrl($model_class_name) . '/delete/' . $obj_id;
    }

    /**
     * генерирует ссылку на удаление объекта
     */
    public static function getDeleteUrlForObj($obj)
    {
        // добавляем \ в начале имени класса - мы всегда работаем с классами в глобальном неймспейсе
        $obj_class_name = '\\' . get_class($obj);
        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($obj_class_name, 'Skif\Model\InterfaceLoad');

        $obj_id = $obj->getId();

        return static::getDeleteUrl($obj_class_name, $obj_id);
    }

    public static function getSaveUrl($model_class_name, $obj_id)
    {
        return static::getCRUDBaseUrl($model_class_name) . '/save/' . $obj_id;
    }

    public function listAction()
    {
        $model_class_name = static::getModelClassName();

        \Skif\Http::exit403If(!\Skif\CRUD\CRUDUtils::currentUserHasRightsToEditModel($model_class_name));

        \Skif\Utils::assert($model_class_name);

        $context_arr = array();
        if (property_exists($model_class_name, 'crud_default_context_arr_for_list')) {
            $context_arr = $model_class_name::$crud_default_context_arr_for_list;
        }

        if (isset($model_class_name::$crud_model_filtered_field_arr_for_list)) {
            foreach ($model_class_name::$crud_model_filtered_field_arr_for_list as $field_name) {
                if (array_key_exists($field_name, $_GET)) {
                    if ($_GET[$field_name] != '') {
                        $context_arr[$field_name] = $_GET[$field_name];
                    }
                }
            }
        }

        if (array_key_exists('context_arr', $_GET)) {
            $context_arr = $_GET['context_arr'];
        }

        $list_html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'CRUD',
            'list.tpl.php',
            array(
                'model_class_name' => $model_class_name,
                'context_arr' => $context_arr,
                'current_controller_obj' => static::getControllerClassNameByModelClassName($model_class_name)
            )
        );

        $crud_model_class_screen_name_for_list = 'Список';
        if (property_exists($model_class_name, 'crud_model_class_screen_name_for_list')) {
            $crud_model_class_screen_name_for_list = $model_class_name::$crud_model_class_screen_name_for_list;
        }

        echo \Skif\PhpTemplate::renderTemplate(
            static::getLayoutTemplateFile(),
            array(
                'title' => $crud_model_class_screen_name_for_list,
                'content' => $list_html,
                'breadcrumbs_arr' => static::$base_breadcrumbs
            )
        );
    }

    /**
     * Выводит форму создания объекта.
     * Принимает в запросе контекст (набор полей со значениями) и передает его на экшен создания объекта.
     */
    public function addAction()
    {
        $model_class_name = static::getModelClassName();

        \Skif\Http::exit403If(!\Skif\CRUD\CRUDUtils::currentUserHasRightsToEditModel($model_class_name));

        \Skif\Utils::assert($model_class_name);

        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceLoad');
        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceSave');

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'CRUD',
            'add_form.tpl.php',
            array(
                'model_class_name' => $model_class_name,
                'current_controller_obj' => static::getControllerClassNameByModelClassName($model_class_name)
            )
        );

        $breadcrumbs_arr = static::$base_breadcrumbs;

        if (!property_exists($model_class_name, 'show_models_list_link')) {
            $show_models_list_link = true;
        } else {
            $show_models_list_link = $model_class_name::$show_models_list_link;
        }

        if ($show_models_list_link) {
            $crud_model_class_screen_name_for_list = $model_class_name;
            if (property_exists($model_class_name, 'crud_model_class_screen_name_for_list')) {
                $crud_model_class_screen_name_for_list = $model_class_name::$crud_model_class_screen_name_for_list;
            }

            $breadcrumbs_arr = array_merge(
                $breadcrumbs_arr,
                array(
                    $crud_model_class_screen_name_for_list => static::getListUrl($model_class_name)
                )
            );
        }

        echo \Skif\PhpTemplate::renderTemplate(
            static::getLayoutTemplateFile(),
            array(
                'title' => 'Добавление',
                'content' => $html,
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    public function editAction($obj_id)
    {
        $model_class_name = static::getModelClassName();

        \Skif\Http::exit403If(!\Skif\CRUD\CRUDUtils::currentUserHasRightsToEditModel($model_class_name));

        \Skif\Utils::assert($model_class_name);
        \Skif\Utils::assert($obj_id);
        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceLoad');

        $edited_obj = \Skif\CRUD\CRUDUtils::createAndLoadObject($model_class_name, $obj_id);

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'CRUD',
            'edit_form.tpl.php',
            array(
                'model_class_name' => $model_class_name,
                'obj' => $edited_obj,
                'current_controller_obj' => static::getControllerClassNameByModelClassName($model_class_name)
            )
        );

        $breadcrumbs_arr = static::$base_breadcrumbs;

        if (!property_exists($model_class_name, 'show_models_list_link')) {
            $show_models_list_link = true;
        } else {
            $show_models_list_link = $model_class_name::$show_models_list_link;
        }

        if ($show_models_list_link) {
            $crud_model_class_screen_name_for_list = $model_class_name;
            if (property_exists($model_class_name, 'crud_model_class_screen_name_for_list')) {
                $crud_model_class_screen_name_for_list = $model_class_name::$crud_model_class_screen_name_for_list;
            }

            $breadcrumbs_arr = array_merge(
                $breadcrumbs_arr,
                array(
                    $crud_model_class_screen_name_for_list => static::getListUrl($model_class_name)
                )
            );
        }

        /*
        $container_obj = \Skif\CRUD\CRUDUtils::getObjContainerObj($edited_obj);
        if ($container_obj) {
            $container_obj_url = static::getEditUrlForObj($container_obj);
            $container_obj_full_title = \Skif\CRUD\CRUDUtils::getFullObjectTitle($container_obj);
            $breadcrumbs_arr[$container_obj_full_title] = $container_obj_url;
        }
        */

        echo \Skif\PhpTemplate::renderTemplate(
            static::getLayoutTemplateFile(),
            array(
                'title' => \Skif\CRUD\CRUDUtils::getModelTitleForObj($edited_obj),
                'content' => $html,
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    /**
     * Заполнение полей значениями из формы
     * @param $model_class_name
     * @param $redirect_url
     * @return array
     */
    protected static function fillPropValuesArrFromRequest($model_class_name, $redirect_url)
    {
        $reflect = new \ReflectionClass($model_class_name);

        $new_prop_values_arr = array();

        foreach ($reflect->getProperties() as $prop_obj) {
            // игнорируем статические свойства класса - они относятся не к объекту, а только к классу
            // (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
            if ($prop_obj->isStatic()) {
                continue;
            }

            $prop_name = $prop_obj->getName();

            if (!array_key_exists($prop_name, $_REQUEST)) {
                continue;
            }

            // Проверка на заполнение обязательных полей
            if ((($_REQUEST[$prop_name] == '') && (\Skif\CRUD\CRUDUtils::isRequiredField($model_class_name, $prop_obj->getName())))) {
                \Skif\Messages::setError('поле ' . $prop_obj->getName() . ' обязательно для заполнения');
                \Skif\Http::redirect($redirect_url);
            }

            $new_prop_values_arr[$prop_name] = $_REQUEST[$prop_name];
        }

        return $new_prop_values_arr;
    }

    public function saveAction($obj_id)
    {
        $model_class_name = static::getModelClassName();

        \Skif\Http::exit403If(!\Skif\CRUD\CRUDUtils::currentUserHasRightsToEditModel($model_class_name));

        \Skif\Utils::assert($model_class_name);
        \Skif\Utils::assert($obj_id);

        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceLoad');
        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceSave');


        $obj = \Skif\CRUD\CRUDUtils::createAndLoadObject($model_class_name, $obj_id);

        $redirect_url = static::getEditUrlForObj($obj);

        if (array_key_exists('destination', $_REQUEST)) {
            $redirect_url = $_REQUEST['destination'];
        }

        $new_prop_values_arr = static::fillPropValuesArrFromRequest($model_class_name, $redirect_url);

        $obj = \Skif\CRUD\CRUDUtils::setObjectFieldsFromArray($obj, $new_prop_values_arr);

        $obj->save();


        \Skif\Messages::setMessage('Изменения сохранены');

        \Skif\Http::redirect($redirect_url);
    }

    protected static function afterCreate($obj_id)
    {

    }

    public function createAction()
    {
        $model_class_name = static::getModelClassName();

        \Skif\Http::exit403If(!\Skif\CRUD\CRUDUtils::currentUserHasRightsToEditModel($model_class_name));

        \Skif\Utils::assert($model_class_name);
        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceLoad');
        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceSave');


        $redirect_url = static::getAddUrl($model_class_name);

        $new_prop_values_arr = static::fillPropValuesArrFromRequest($model_class_name, $redirect_url);

        if (!static::createValidation()) {
            \Skif\Http::redirect($redirect_url);
        }

        $obj = new $model_class_name;
        $obj = \Skif\CRUD\CRUDUtils::setObjectFieldsFromArray($obj, $new_prop_values_arr);

        $obj->save();

        static::afterCreate($obj->getId());

        $redirect_url = static::getEditUrl($model_class_name, $obj->getId());

        if (array_key_exists('destination', $_REQUEST)) {
            $redirect_url = $_REQUEST['destination'];
            $separator = '?';
            if (mb_strpos($redirect_url, '?')) {
                $separator = '&';
            }
            $redirect_url .= $separator . 'crud_obj_model_class=' . urlencode($model_class_name) . '&crud_obj_id=' . $obj->getId();
        }

        \Skif\Messages::setMessage('Изменения сохранены');

        \Skif\Http::redirect($redirect_url);
    }

    protected static function afterDelete($obj)
    {

    }

    public function deleteAction($obj_id)
    {
        $model_class_name = static::getModelClassName();

        \Skif\Http::exit403If(!\Skif\CRUD\CRUDUtils::currentUserHasRightsToEditModel($model_class_name));

        \Skif\Utils::assert($model_class_name);
        \Skif\Utils::assert($obj_id);
        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceDelete');

        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceDelete');

        if (property_exists($model_class_name, 'crud_related_models_arr')) {

            foreach ($model_class_name::$crud_related_models_arr as $related_model_class_name => $related_model_data) {
                \Skif\Utils::assert(array_key_exists('link_field', $related_model_data));
                $related_objs_ids_arr = \Skif\CRUD\CRUDUtils::getObjIdsArrayForModel($related_model_class_name, array($related_model_data['link_field'] => $obj_id));
                if (count($related_objs_ids_arr) > 0) {
                    throw new \Exception('Related model exists, can\'t delete entity. Delete related entities first.');
                }

            }
        }

        // удаление объекта
        $obj = \Skif\CRUD\CRUDUtils::createAndLoadObject($model_class_name, $obj_id);
        $obj->delete();

        static::afterDelete($obj);

        $redirect_url = static::getListUrl($model_class_name);
        if (array_key_exists('destination', $_GET)) {
            $redirect_url = $_GET['destination'];
        }

        \Skif\Messages::setMessage('Удаление выполнено успешно');

        \Skif\Http::redirect($redirect_url);
    }

}