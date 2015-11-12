<?php

namespace Skif\Model;

/**
 * Поддержка классом этого интерфейса означает, что класс умеет создавать свои экземпляры, кэшировать их и сбрасывать кэш при изменениях.
 * Базовая реализация есть в трейте FactoryTrait.
 * Interface InterfaceFactory
 * @package Skif\Model
 */
interface InterfaceFactory {
    public static function factory($id_to_load, $exception_if_not_loaded = true);
    public static function getMyGlobalizedClassName();
    static public function removeObjFromCacheById($id_to_remove);
    static public function afterUpdate($id);
    public function checkBeforeDelete();
    public function afterDelete();
}