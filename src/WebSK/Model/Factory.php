<?php

namespace WebSK\Model;

use WebSK\Skif\ConfWrapper;
use Websk\Cache\CacheWrapper;
use Websk\Utils\Assert;

/**
 * Базовая фабрика объектов.
 */
class Factory
{
    /**
     * @param $class_name
     * @param $object_id
     * @return string
     */
    protected static function getObjectCacheId($class_name, $object_id)
    {
        return $class_name . '::' . $object_id;
    }

    /**
     * @param $class_name
     * @param $object_id
     */
    public static function removeObjectFromCache($class_name, $object_id)
    {
        $cache_key = self::getObjectCacheId($class_name, $object_id);
        CacheWrapper::delete($cache_key);
    }

    /**
     * @param $class_name
     * @param $object_id
     * @return bool|mixed|null
     * @throws \Exception
     */
    public static function createAndLoadObject($class_name, $object_id)
    {
        $cache_key = self::getObjectCacheId($class_name, $object_id);

        $cached_obj = CacheWrapper::get($cache_key);

        if ($cached_obj !== false) {
            return $cached_obj;
        }

        $obj = new $class_name;

        $object_is_loaded = call_user_func_array(array($obj, "load"), array($object_id));

        if (!$object_is_loaded) {
            return null;
        }

        $cache_ttl_seconds = ConfWrapper::value('cache.expire');

        if ($obj instanceof InterfaceCacheTtlSeconds) {
            $cache_ttl_seconds = $obj->getCacheTtlSeconds();
        }

        CacheWrapper::set($cache_key, $obj, $cache_ttl_seconds);

        return $obj;
    }

    /**
     * @param $class_name
     * @param $fields_arr
     * @return bool|mixed|null
     * @throws \Exception
     */
    public static function createAndLoadObjectByFieldsArr($class_name, $fields_arr)
    {
        $obj = new $class_name;

        if (!($obj instanceof InterfaceLoad)) {
            Assert::assert($obj);
        }

        $id_to_load = call_user_func_array(array($obj, "getIdByFieldNamesArr"), array($fields_arr));

        return self::createAndLoadObject($class_name, $id_to_load);
    }
}
