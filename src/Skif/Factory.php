<?php

namespace Skif;

/**
 * Базовая фабрика объектов.
 */
class Factory
{
    protected static function getObjectCacheId($class_name, $object_id){
        return $class_name . '::' . $object_id;
    }

    public static function removeObjectFromCache($class_name, $object_id)
    {
        $cache_key = self::getObjectCacheId($class_name, $object_id);
        \Skif\Cache\CacheWrapper::delete($cache_key);
    }

    public static function createAndLoadObject($class_name, $object_id)
    {
        $cache_key = self::getObjectCacheId($class_name, $object_id);

        $cached_obj = \Skif\Cache\CacheWrapper::get($cache_key);

        if ($cached_obj !== false) {
            return $cached_obj;
        }

        $obj = new $class_name;

        $object_is_loaded = call_user_func_array(array($obj, "load"), array($object_id));

        if (!$object_is_loaded) {
            return null;
        }

        $cache_ttl_seconds = Conf\ConfWrapper::value('cache.expire');

        if ($obj instanceof \Skif\Model\InterfaceCacheTtlSeconds) {
            $cache_ttl_seconds = $obj->getCacheTtlSeconds();
        }

        \Skif\Cache\CacheWrapper::set($cache_key, $obj, $cache_ttl_seconds);

        return $obj;
    }

}