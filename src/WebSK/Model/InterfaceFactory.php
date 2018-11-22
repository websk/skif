<?php

namespace WebSK\Model;

/**
 * Поддержка классом этого интерфейса означает, что класс умеет создавать свои экземпляры, кэшировать их и сбрасывать кэш при изменениях.
 * Базовая реализация есть в трейте FactoryTrait.
 * Interface InterfaceFactory
 * @package WebSK\Model
 */
interface InterfaceFactory
{
    public static function factory($id_to_load, $exception_if_not_loaded = true);

    public static function factoryByFieldsArr($fields_arr, $exception_if_not_loaded = true);

    public static function getMyGlobalizedClassName();

    public static function removeObjFromCacheById($id_to_remove);

    public static function afterUpdate($id);

    public function beforeDelete();

    public function afterDelete();
}