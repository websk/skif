<?php
/**
 * Базовая реализация интерфейса \Skif\Model\InterfaceFactory
 */

namespace Skif\Model;

trait FactoryTrait
{
    /**
     * Возвращает глобализованное имя класса модели.
     * @return string
     */
    public static function getMyGlobalizedClassName()
    {
        $class_name = get_called_class(); // "Gets the name of the class the static method is called in."
        $class_name = \Skif\Model\Helper::globalizeClassName($class_name);

        return $class_name;
    }

    /**
     * Базовая загрузка объекта
     * @param $id_to_load
     * @param bool|true $exception_if_not_loaded
     * @return null|object
     * @throws \Exception
     */
    public static function factory($id_to_load, $exception_if_not_loaded = true)
    {
        $class_name = self::getMyGlobalizedClassName();
        $obj = \Skif\Factory::createAndLoadObject($class_name, $id_to_load);

        if ($exception_if_not_loaded) {
            \Skif\Utils::assert($obj);
        }

        return $obj;
    }

    public static function factoryByFieldsArr($fields_arr, $exception_if_not_loaded = true)
    {
        $class_name = self::getMyGlobalizedClassName();
        $obj = \Skif\Factory::createAndLoadObjectByFieldsArr($class_name, $fields_arr);

        if ($exception_if_not_loaded) {
            \Skif\Utils::assert($obj);
        }

        return $obj;
    }

    public static function removeObjFromCacheById($id_to_remove)
    {
        $class_name = self::getMyGlobalizedClassName();
        \Skif\Factory::removeObjectFromCache($class_name, $id_to_remove);
    }

    /**
     * Базовая обработка изменения.
     * Если на это событие есть подписчики - нужно переопределить обработчик в самом классе и там eventmanager::invoke, где уже подписать остальных подписчиков.
     * сделано статиками чтобы можно было вызывать для других объектов не создавая, только по id.
     */
    public static function afterUpdate($id)
    {
        self::removeObjFromCacheById($id);
    }

    public function BeforeDelete()
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
        self::removeObjFromCacheById($this->id);
    }
}