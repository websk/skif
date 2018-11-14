<?php
/**
 * Базовая реализация интерфейса \Skif\Model\InterfaceFactory
 */

namespace Skif\Model;

use Websk\Utils\Assert;

/**
 * Trait FactoryTrait
 * @package Skif\Model
 */
trait FactoryTrait
{
    /**
     * Возвращает глобализованное имя класса модели.
     * @return string
     */
    public static function getMyGlobalizedClassName()
    {
        $class_name = get_called_class(); // "Gets the name of the class the static method is called in."
        $class_name = Helper::globalizeClassName($class_name);

        return $class_name;
    }

    /**
     * Базовая загрузка объекта по Id
     * @param $id_to_load
     * @param bool|true $exception_if_not_loaded
     * @return $this
     */
    public static function factory($id_to_load, $exception_if_not_loaded = true)
    {
        $class_name = self::getMyGlobalizedClassName();
        $obj = Factory::createAndLoadObject($class_name, $id_to_load);

        if ($exception_if_not_loaded) {
            Assert::assert($obj);
        }

        return $obj;
    }

    /**
     * Загрузка объекта по набору полей
     * @param $fields_arr - array($field_name => $field_value)
     * @param bool|true $exception_if_not_loaded
     * @return $this
     */
    public static function factoryByFieldsArr($fields_arr, $exception_if_not_loaded = true)
    {
        $class_name = self::getMyGlobalizedClassName();
        $obj = Factory::createAndLoadObjectByFieldsArr($class_name, $fields_arr);

        if ($exception_if_not_loaded) {
            Assert::assert($obj);
        }

        return $obj;
    }

    /**
     * @param $id_to_remove
     */
    public static function removeObjFromCacheById($id_to_remove)
    {
        $class_name = self::getMyGlobalizedClassName();
        Factory::removeObjectFromCache($class_name, $id_to_remove);
    }

    /**
     * Базовая обработка изменения.
     * Если на это событие есть подписчики - нужно переопределить обработчик в самом классе и там eventmanager::invoke, где уже подписать остальных подписчиков.
     * сделано статиками чтобы можно было вызывать для других объектов не создавая, только по id.
     * @param $id
     */
    public static function afterUpdate($id)
    {
        $model_class_name = self::getMyGlobalizedClassName();

        if (isset($model_class_name::$depends_on_models_arr)) {
            foreach ($model_class_name::$depends_on_models_arr as $depends_model_class_name => $depends_model_data) {
                Assert::assert(array_key_exists('link_field', $depends_model_data));

                $model_obj = Factory::createAndLoadObject($model_class_name, $id);

                $reflect = new \ReflectionClass($model_obj);
                $property_obj = $reflect->getProperty($depends_model_data['link_field']);
                $property_obj->setAccessible(true);

                $depends_id = $property_obj->getValue($model_obj);

                $depends_model_class_name::afterUpdate($depends_id);
            }
        }

        self::removeObjFromCacheById($id);
    }

    public function beforeDelete()
    {
        return true;
    }

    /**
     * Метод чистки после удаления объекта.
     * Поскольку модели уже нет в базе, этот метод должен использовать только данные объекта в памяти:
     * - не вызывать фабрику для этого объекта
     * - не использовать геттеры (они могут обращаться к базе)
     * - не быть статическим: работает в контексте конкретного объекта
     */
    public function afterDelete()
    {
        $model_class_name = self::getMyGlobalizedClassName();

        if (isset($model_class_name::$depends_on_models_arr)) {
            foreach ($model_class_name::$depends_on_models_arr as $depends_model_class_name => $depends_model_data) {
                Assert::assert(array_key_exists('link_field', $depends_model_data));

                $reflect = new \ReflectionClass($this);
                $property_obj = $reflect->getProperty($depends_model_data['link_field']);
                $property_obj->setAccessible(true);

                $depends_id = $property_obj->getValue($this);

                $depends_model_class_name::afterUpdate($depends_id);
            }
        }

        self::removeObjFromCacheById($this->id);
    }
}
