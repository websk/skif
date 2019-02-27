<?php

namespace WebSK\Skif\CRUD\ModelReferenceWidget;

use WebSK\Model\InterfaceGetTitle;
use WebSK\Skif\CRUD\CRUDController;
use WebSK\Views\PhpRender;

class ModelReferenceWidget
{

    /**
     * Отображает виджет
     * @param $field_name
     * @param $field_value
     * @param $widget_options
     * @return string
     */
    public static function renderWidget($field_name, $field_value, $widget_options)
    {
        $widget_options['field_name'] = $field_name;
        $widget_options['field_value'] = $field_value;

        $html = PhpRender::renderTemplateInViewsDir(
            'model_id.tpl.php',
            $widget_options
        );

        return $html;
    }

    /**
     * Возвращает загруженный экземпляр модели
     * @param $model_class_name
     * @param $model_id
     * @return mixed
     * @throws \Exception
     */
    public static function getModelObject($model_class_name, $model_id)
    {
        if (!class_exists($model_class_name)) {
            throw new \Exception('Класса ' . $model_class_name . ' не существует');
        }

        $model_obj = new $model_class_name;

        $model_is_loaded = $model_obj->load($model_id);

        if ($model_is_loaded) {
            return $model_obj;
        }

        return null;
    }

    /**
     * Возвращает видимый заголовок экземпляра модели
     */
    public static function widgetGetModelTitleByIdAction()
    {
        $model_class_name = urldecode($_POST['model_class_name']);
        $model_id = $_POST['model_id'];

        $model_obj = self::getModelObject($model_class_name, $model_id);

        if (!$model_obj) {
            echo json_encode(
                array(
                    'success' => false,
                    'error' => 'Объект с ID ' . $model_id . ' - не найден'
                )
            );

            return;
        }

        if (!($model_obj instanceof InterfaceGetTitle)) {
            echo json_encode(
                array(
                    'success' => false,
                    'error' => 'Модель ' . get_class($model_obj) . ' должна реализовывать интерфейс \Skif\Model\InterfaceGetTitle'
                )
            );

            return;
        }

        echo json_encode(
            array(
                'success' => true,
                'display_title' => $model_obj->getTitle(),
                'href' => CRUDController::getEditUrl($model_class_name, $model_id)
            )
        );
    }
}
