<?php

namespace Skif;

/**
 * Базовая фабрика объектов.
 */
class Factory
{
    protected static function getObjectCacheId($class_name, $transfer_key)
    {
        str_replace('\\', '_', $class_name);

        // TODO: убрать возню с массивом, area не используется
        return array('cid' => 'obj1__' . $class_name . '_' . $transfer_key, 'area' => 'cache');
    }

    protected static function getObjectFromCache($class_name, $transfer_key)
    {
        $cid_arr = self::getObjectCacheId($class_name, $transfer_key);
        $cache = \Skif\Cache\CacheWrapper::get($cid_arr['cid']);

        return $cache;
    }

    public static function removeObjectFromCache($class_name)
    {
        $raw_keys_arr = array_slice(func_get_args(), 1);
        $keys_arr = self::preprocessKeys($raw_keys_arr);
        $transfer_key = md5(serialize($keys_arr));

        $cid_arr = self::getObjectCacheId($class_name, $transfer_key);
        $cid = $cid_arr['cid'];

        \Skif\Cache\CacheWrapper::delete($cid);
    }

    /**
     * Приводит значения всех элементов массива к строкам.
     * Нужно для того, чтобы при сериализации строки массивов выглядели всегда одинаково, независимо от типа данных.
     * (например, идентификатор ноды може прийти и как строка, и как число, и будет иметь разный хэш в зависимости от наличия кавычек)
     * @param $raw_keys_arr
     * @return array
     */
    protected static function preprocessKeys($raw_keys_arr)
    {
        $keys_arr = array();

        foreach ($raw_keys_arr as $k => $value) {
            $keys_arr[$k] = (string)$value;
        }

        return $keys_arr;
    }


    /**
     * Создает новый объект указанного класса и вызывает для него load().
     * В load() передаются все параметры, которые получила эта функция после имени класса.
     * @param $class_name Имя класса, объект которого создаем.
     * @return null|object Если удалось создать и загрузить объект - возвращается этот объект. Иначе (например, не удалось загрузить) - возвращает null.
     * @throws \Exception
     */
    public static function createAndLoadObject($class_name)
    {
        if ($class_name == '') {
            throw new \Exception('Factory::createAndLoadObject(): empty class_name');
        }

        $use_cache = true;

        $raw_keys_arr = array_slice(func_get_args(), 1);

        $keys_arr = self::preprocessKeys($raw_keys_arr);

        $transfer_key = md5(serialize($keys_arr));

        if ($use_cache) {
            $cached_obj = self::getObjectFromCache($class_name, $transfer_key);

            if ($cached_obj !== false) {
                return $cached_obj;
            }
        }

        $obj = new $class_name;

        // TODO: check whether implements some interface instead of method_exists? think over
        if (!method_exists($obj, "load")) {
            throw new \Exception("createAndLoadObject for class without load() method: " . $class_name);
        }

        // call load() method for object, passing keys as parameters
        $object_is_loaded = call_user_func_array(array($obj, "load"), $keys_arr);

        if (!$object_is_loaded) {
            return null;
        }

        // store to cache

        if ($use_cache) {
            $cache_ttl_seconds = Conf\ConfWrapper::value('cache.expire');

            if (isset($obj->__cache_ttl_seconds)) {
                $cache_ttl_seconds = $obj->__cache_ttl_seconds;
            }

            $cache_id_arr = self::getObjectCacheId($class_name, $transfer_key);
            \Skif\Cache\CacheWrapper::set($cache_id_arr['cid'], $obj, $cache_ttl_seconds);
        }

        return $obj;
    }

}