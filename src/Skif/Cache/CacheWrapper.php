<?php

namespace Skif\Cache;

use Skif\Cache\Engines\CacheEngineInterface;

class CacheWrapper
{
    protected static $storage_arr = array();

    /**
     * @param $key
     * @return bool|mixed
     */
    public static function get($key)
    {
        if (isset(self::$storage_arr[$key])) {
            return self::$storage_arr[$key];
        }

        $cache_obj = CacheFactory::getCacheObj();
        if (!($cache_obj instanceof CacheEngineInterface)) {
            return false;
        }

        $value = $cache_obj::get($key);

        if ($value !== false) {
            self::$storage_arr[$key] = $value;
        }

        return $value;
    }

    /**
     * @param $key
     * @return bool
     */
    public static function delete($key)
    {
        unset(self::$storage_arr[$key]);

        $cache_obj = CacheFactory::getCacheObj();
        if (!($cache_obj instanceof CacheEngineInterface)) {
            return false;
        }

        return $cache_obj::delete($key);
    }

    /**
     * @param $key
     * @param $value
     * @param int $expire
     * @return bool
     * @throws \Exception
     */
    public static function set($key, $value, $expire = -1)
    {
        if ($key == '' || is_object($key)) {
            throw new \Exception('static storage wrong key in set');
        }

        self::$storage_arr[$key] = $value;

        $cache_obj = CacheFactory::getCacheObj();
        if (!($cache_obj instanceof CacheEngineInterface)) {
            return false;
        }

        return $cache_obj::set($key, $value, $expire);
    }

    /**
     * Обновляет время жизни кеша
     * @param string $cache_key
     * @param $expire
     */
    public static function updateExpireByCacheKey($cache_key, $expire)
    {
        $cached = self::get($cache_key);
        if ($cached !== false) {
            self::set($cache_key, $cached, $expire);
        }
    }
}