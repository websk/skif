<?php

namespace Skif\CRUD;

/**
 * CRUD проверяет, реализует ли модель функционал моделей.
 * Если умеет загружаться - круд может показывать такие модели.
 * Если умеет сохраняться - круд может редактировать такие модели.
 */
class CRUDController
{
    public static $base_breadcrumbs = array();
    protected static $model_class_name = '';

    public static function getBaseUrl($model_class_name)
    {
        return '/crud/' . urlencode($model_class_name);
    }

    public static function getListUrl($model_class_name)
    {
        return static::getBaseUrl($model_class_name);
    }

    public static function getCreateUrl($model_class_name)
    {
        return static::getBaseUrl($model_class_name) . '/create';
    }

    public static function getAddUrl($model_class_name)
    {
        return static::getBaseUrl($model_class_name) . '/add';
    }

    /**
     * Генерирует ссылку на редактор объекта
     */
    public static function getEditUrl($model_class_name, $obj_id)
    {
        return static::getBaseUrl($model_class_name) . '/edit/' . $obj_id;
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
        return static::getBaseUrl($model_class_name) . '/delete/' . $obj_id;
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
        return static::getBaseUrl($model_class_name) . '/save/' . $obj_id;
    }

    public function listAction($model_class_name = '')
    {
        if (!$model_class_name) {
            $model_class_name = static::$model_class_name;
        }

        \Skif\Http::exit403If(!\Skif\CRUD\CRUDUtils::currentUserHasRightsToEditModel($model_class_name));

        \Skif\Utils::assert($model_class_name);

        $context_arr = array();
        if (array_key_exists('context_arr', $_GET)) {
            $context_arr = $_GET['context_arr'];
        }

        $list_html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'CRUD',
            'list.tpl.php',
            array(
                'model_class_name' => $model_class_name,
                'context_arr' => $context_arr,
            )
        );

        $crud_model_class_screen_name_for_list = 'Список';
        if (property_exists($model_class_name, 'crud_model_class_screen_name_for_list')) {
            $crud_model_class_screen_name_for_list = $model_class_name::$crud_model_class_screen_name_for_list;
        }

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.admin'),
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
     * @param $model_class_name
     */
    public function addAction($model_class_name = '')
    {
        if (!$model_class_name) {
            $model_class_name = static::$model_class_name;
        }

        \Skif\Http::exit403If(!\Skif\CRUD\CRUDUtils::currentUserHasRightsToEditModel($model_class_name));

        \Skif\Utils::assert($model_class_name);

        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceLoad');
        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceSave');

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'CRUD',
            'add_form.tpl.php',
            array(
                'model_class_name' => $model_class_name
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
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'title' => 'Добавление',
                'content' => $html,
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    public function editAction($obj_id)
    {
        $current_url_no_query = \Skif\UrlManager::getUriNoQueryString();

        if (preg_match('/crud/([\d\w\%]+)/(.+)', $current_url_no_query, $matches_arr)) {
            $model_class_name = $matches_arr[1];
        }

        if (!isset($model_class_name)) {
            $model_class_name = static::$model_class_name;
        }

        \Skif\Http::exit403If(!\Skif\CRUD\CRUDUtils::currentUserHasRightsToEditModel($model_class_name));

        \Skif\Utils::assert($model_class_name);
        \Skif\Utils::assert($obj_id);
        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceLoad');

        $edited_obj = \Skif\CRUD\CRUDUtils::createAndLoadObject($model_class_name, $obj_id);

        $html = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'CRUD',
            'edit_form.tpl.php',
            array(
                'obj' => $edited_obj
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
            \Skif\Conf\ConfWrapper::value('layout.admin'),
            array(
                'title' => \Skif\CRUD\CRUDUtils::getModelTitleForObj($edited_obj),
                'content' => $html,
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    public function saveAction($model_class_name = '', $obj_id)
    {
        if (!$model_class_name) {
            $model_class_name = static::$model_class_name;
        }

        \Skif\Http::exit403If(!\Skif\CRUD\CRUDUtils::currentUserHasRightsToEditModel($model_class_name));

        \Skif\Utils::assert($model_class_name);
        \Skif\Utils::assert($obj_id);
        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceLoad');
        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceSave');

        // чтение данных из формы
        $new_prop_values_arr = array();
        $reflect = new \ReflectionClass($model_class_name);

        foreach ($reflect->getProperties() as $prop_obj) {
            if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
                $prop_name = $prop_obj->getName();
                if (array_key_exists($prop_name, $_POST)) {
                    // Проверка на заполнение обязательных полей
                    if ((($_POST[$prop_name] == '') && (\Skif\CRUD\CRUDUtils::isRequiredField($model_class_name, $prop_obj->getName())))) {
                        throw new \Exception('поле ' . $prop_obj->getName() . ' обязательно для заполнения');
                    }
                    $new_prop_values_arr[$prop_name] = $_POST[$prop_name];
                }
            }
        }

        // сохранение
        $obj = \Skif\CRUD\CRUDUtils::createAndLoadObject($model_class_name, $obj_id);

        $obj = \Skif\CRUD\CRUDUtils::setObjectFieldsFromArray($obj, $new_prop_values_arr);
        $obj->save();

        $redirect_url = static::getEditUrlForObj($obj);

        if (array_key_exists('destination', $_POST)) {
            $redirect_url = $_POST['destination'];
        }

        \Skif\Http::redirect($redirect_url);
    }

    public function createAction($model_class_name = '')
    {
        if (!$model_class_name) {
            $model_class_name = static::$model_class_name;
        }

        \Skif\Http::exit403If(!\Skif\CRUD\CRUDUtils::currentUserHasRightsToEditModel($model_class_name));

        \Skif\Utils::assert($model_class_name);
        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceLoad');
        \Skif\CRUD\CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, 'Skif\Model\InterfaceSave');

        $new_prop_values_arr = array();
        $reflect = new \ReflectionClass($model_class_name);

        foreach ($reflect->getProperties() as $prop_obj) {
            if (!$prop_obj->isStatic()) { // игнорируем статические свойства класса - они относятся не к объекту, а только к классу (http://www.php.net/manual/en/language.oop5.static.php), и в них хранятся настройки ActiveRecord и CRUD
                $prop_name = $prop_obj->getName();
                if (array_key_exists($prop_name, $_POST)) {
                    // Проверка на заполнение обязательных полей
                    if ((($_POST[$prop_name] == '') && (\Skif\CRUD\CRUDUtils::isRequiredField($model_class_name, $prop_obj->getName())))) {
                        throw new \Exception('поле ' . $prop_obj->getName() . ' обязательно для заполнения');
                    }
                    $new_prop_values_arr[$prop_name] = $_POST[$prop_name];
                }
            }
        }

        $obj = new $model_class_name;
        $obj = \Skif\CRUD\CRUDUtils::setObjectFieldsFromArray($obj, $new_prop_values_arr);

        $obj->save();

        $redirect_url = static::getEditUrl($model_class_name, $obj->getId());

        if (array_key_exists('destination', $_POST)) {
            $redirect_url = $_POST['destination'];
            $separator = '?';
            if (mb_strpos($redirect_url, '?')) {
                $separator = '&';
            }
            $redirect_url .= $separator . 'crud_obj_model_class=' . urlencode($model_class_name) . '&crud_obj_id=' . $obj->getId();
        }

        \Skif\Http::redirect($redirect_url);
    }

    public function deleteAction($model_class_name = '', $obj_id)
    {
        if (!$model_class_name) {
            $model_class_name = static::$model_class_name;
        }

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

        $redirect_url = '';
        if (array_key_exists('destination', $_GET)) {
            $redirect_url = $_GET['destination'];
        }

        \Skif\Utils::assert($redirect_url);
        \Skif\Http::redirect($redirect_url);
    }

}