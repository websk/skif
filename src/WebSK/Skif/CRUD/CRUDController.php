<?php

namespace WebSK\Skif\CRUD;

use WebSK\Skif\BaseController;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceLoad;
use WebSK\Model\InterfaceSave;
use WebSK\Skif\SkifPhpRender;
use WebSK\Slim\ConfWrapper;
use WebSK\Utils\Messages;
use WebSK\Utils\Assert;
use WebSK\Utils\Exits;
use WebSK\Utils\Redirects;
use WebSK\Utils\Url;

/**
 * CRUD проверяет, реализует ли модель функционал моделей.
 * Если умеет загружаться - круд может показывать такие модели.
 * Если умеет сохраняться - круд может редактировать такие модели.
 */
class CRUDController extends BaseController
{
    protected static $model_class_name = '';
    protected static $controller_class_name = '';

    protected static function getLayoutTemplateFile()
    {
        return ConfWrapper::value('layout.admin');
    }

    protected static function getBreadcrumbsArr()
    {
        return array();
    }

    public static function getControllerClassNameByModelClassName($model_class_name)
    {
        if (static::$controller_class_name) {
            return static::$controller_class_name;
        }

        if (class_exists($model_class_name . 'Controller')) {
            return $model_class_name . 'Controller';
        }

        return self::class;
    }

    public static function getModelClassName()
    {
        if (static::$model_class_name) {
            return static::$model_class_name;
        }

        $current_url_no_query = Url::getUriNoQueryString();

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

        CRUDUtils::exceptionIfClassNotImplementsInterface($obj_class_name, InterfaceLoad::class);

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
        CRUDUtils::exceptionIfClassNotImplementsInterface($obj_class_name, InterfaceLoad::class);

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

        Exits::exit403If(!CRUDUtils::currentUserHasRightsToListModel($model_class_name));

        Assert::assert($model_class_name);

        $list_html = static::renderList();

        $crud_model_class_screen_name_for_list = 'Список';
        if (property_exists($model_class_name, 'crud_model_class_screen_name_for_list')) {
            $crud_model_class_screen_name_for_list = $model_class_name::$crud_model_class_screen_name_for_list;
        }

        echo SkifPhpRender::renderTemplate(
            static::getLayoutTemplateFile(),
            array(
                'title' => $crud_model_class_screen_name_for_list,
                'content' => $list_html,
                'breadcrumbs_arr' => static::getBreadcrumbsArr()
            )
        );
    }

    /**
     * @param string $model_class_name
     * @param array $objs_ids_arr
     * @return string
     */
    public static function renderList(string $model_class_name = '', array $objs_ids_arr = [])
    {
        if (!$model_class_name) {
            $model_class_name = static::getModelClassName();
        }

        $context_arr = [];
        $filter = '';

        if (!$objs_ids_arr) {
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

            if (isset($_GET['filter'])) {
                $filter = $_GET['filter'];
            }

            $objs_ids_arr = CRUDUtils::getObjIdsArrayForModel($model_class_name, $context_arr, $filter);
        }

        $list_html = SkifPhpRender::renderTemplateBySkifModule(
            'CRUD',
            'list.tpl.php',
            array(
                'model_class_name' => $model_class_name,
                'objs_ids_arr' => $objs_ids_arr,
                'context_arr' => $context_arr,
                'filter' => $filter,
                'current_controller_obj' => static::getControllerClassNameByModelClassName($model_class_name)
            )
        );

        return $list_html;
    }

    /**
     * Выводит форму создания объекта.
     * Принимает в запросе контекст (набор полей со значениями) и передает его на экшен создания объекта.
     */
    public function addAction()
    {
        $model_class_name = static::getModelClassName();

        Exits::exit403If(!CRUDUtils::currentUserHasRightsToEditModel($model_class_name, 'new'));

        Assert::assert($model_class_name);

        CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, InterfaceLoad::class);
        CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, InterfaceSave::class);

        $html = SkifPhpRender::renderTemplateBySkifModule(
            'CRUD',
            'add_form.tpl.php',
            array(
                'model_class_name' => $model_class_name,
                'current_controller_obj' => static::getControllerClassNameByModelClassName($model_class_name)
            )
        );

        $breadcrumbs_arr = static::getBreadcrumbsArr();

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

        $crud_model_class_screen_name_for_add = 'Добавление';
        if (property_exists($model_class_name, 'crud_model_class_screen_name_for_add')) {
            $crud_model_class_screen_name_for_add = $model_class_name::$crud_model_class_screen_name_for_add;
        }

        echo SkifPhpRender::renderTemplate(
            static::getLayoutTemplateFile(),
            array(
                'title' => $crud_model_class_screen_name_for_add,
                'content' => $html,
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }

    public function editAction($obj_id)
    {
        $model_class_name = static::getModelClassName();

        Exits::exit403If(!CRUDUtils::currentUserHasRightsToEditModel($model_class_name, $obj_id));

        Assert::assert($model_class_name);
        Assert::assert($obj_id);
        CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, InterfaceLoad::class);

        $edited_obj = CRUDUtils::createAndLoadObject($model_class_name, $obj_id);

        $html = static::renderEditForm($obj_id);

        $breadcrumbs_arr = static::getBreadcrumbsArr();

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

        echo SkifPhpRender::renderTemplate(
            static::getLayoutTemplateFile(),
            array(
                'title' => CRUDUtils::getModelTitleForObj($edited_obj),
                'content' => $html,
                'breadcrumbs_arr' => $breadcrumbs_arr
            )
        );
    }
    
    public static function renderEditForm($obj_id)
    {
        $model_class_name = static::getModelClassName();

        $edited_obj = CRUDUtils::createAndLoadObject($model_class_name, $obj_id);

        $html = SkifPhpRender::renderTemplateBySkifModule(
            'CRUD',
            'edit_form.tpl.php',
            array(
                'model_class_name' => $model_class_name,
                'obj' => $edited_obj,
                'current_controller_obj' => static::getControllerClassNameByModelClassName($model_class_name)
            )
        );
        
        return $html;
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
            if ((($_REQUEST[$prop_name] == '') && (CRUDUtils::isRequiredField($model_class_name, $prop_obj->getName())))) {
                Messages::setError('поле ' . $prop_obj->getName() . ' обязательно для заполнения');
                Redirects::redirect($redirect_url);
            }

            $new_prop_values_arr[$prop_name] = $_REQUEST[$prop_name];
        }

        return $new_prop_values_arr;
    }

    protected static function afterSave($obj_id)
    {

    }

    public function saveAction($obj_id)
    {
        $model_class_name = static::getModelClassName();

        Exits::exit403If(!CRUDUtils::currentUserHasRightsToEditModel($model_class_name, $obj_id));

        Assert::assert($model_class_name);
        Assert::assert($obj_id);

        CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, InterfaceLoad::class);
        CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, InterfaceSave::class);


        $obj = CRUDUtils::createAndLoadObject($model_class_name, $obj_id);

        $redirect_url = static::getEditUrlForObj($obj);

        if (array_key_exists('destination', $_REQUEST)) {
            $redirect_url = $_REQUEST['destination'];
        }

        $new_prop_values_arr = static::fillPropValuesArrFromRequest($model_class_name, $redirect_url);

        $obj = CRUDUtils::setObjectFieldsFromArray($obj, $new_prop_values_arr);

        $obj->save();

        static::afterSave($obj_id);

        Messages::setMessage('Изменения сохранены');

        Redirects::redirect($redirect_url);
    }

    protected static function afterCreate($obj_id)
    {

    }

    public function createAction()
    {
        $model_class_name = static::getModelClassName();

        Exits::exit403If(!CRUDUtils::currentUserHasRightsToEditModel($model_class_name, 'new'));

        Assert::assert($model_class_name);
        CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, InterfaceLoad::class);
        CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, InterfaceSave::class);


        $redirect_url = static::getAddUrl($model_class_name);

        $new_prop_values_arr = static::fillPropValuesArrFromRequest($model_class_name, $redirect_url);

        if (!static::createValidation()) {
            Redirects::redirect($redirect_url);
        }

        $obj = new $model_class_name;
        $obj = CRUDUtils::setObjectFieldsFromArray($obj, $new_prop_values_arr);

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

        Messages::setMessage('Изменения сохранены');

        Redirects::redirect($redirect_url);
    }

    protected static function afterDelete($obj)
    {
    }

    public function deleteAction($obj_id)
    {
        $model_class_name = static::getModelClassName();

        Exits::exit403If(!CRUDUtils::currentUserHasRightsToEditModel($model_class_name, $obj_id));

        Assert::assert($model_class_name);
        Assert::assert($obj_id);

        CRUDUtils::exceptionIfClassNotImplementsInterface($model_class_name, InterfaceDelete::class);

        $redirect_url = static::getListUrl($model_class_name);
        if (array_key_exists('destination', $_GET)) {
            $redirect_url = $_GET['destination'];
        }


        // удаление объекта
        $obj = CRUDUtils::createAndLoadObject($model_class_name, $obj_id);
        $message = $obj->delete();

        if ($message !== true) {
            Messages::setError($message);
            Redirects::redirect($redirect_url);
        }

        static::afterDelete($obj);

        Messages::setMessage('Удаление выполнено успешно');

        Redirects::redirect($redirect_url);
    }
}
