<?php

namespace Skif\Cache;

class CacheWrapper
{
    static protected $storage_arr = array();
    
    static public function get($key)
    {
    	if (isset(self::$storage_arr[$key])){
        	return self::$storage_arr[$key];
        }
        
        $cache_obj = \Skif\Cache\CacheFactory::getCacheObj();
        if (!$cache_obj->connected) {
            return false;
        }

        $value = $cache_obj->get($key);

        if ($value !== false){
        	self::$storage_arr[$key] = $value;
        }

        return $value;
    }

    static public function delete($key)
    {
    	unset(self::$storage_arr[$key]);

        $cache_obj = \Skif\Cache\CacheFactory::getCacheObj();
        if (!$cache_obj->connected) {
            return false;
        }

        return $cache_obj->delete($key);
    }

    static public function set($key, $value, $expire = -1)
    {
    	if($key == '' || is_object($key)){
    		throw new \Exception('static storage wrong key in set');
    	}
    	
    	self::$storage_arr[$key] = $value;

        $cache_obj = \Skif\Cache\CacheFactory::getCacheObj();
        if (!$cache_obj->connected) {
            return false;
        }

        return $cache_obj->set($key, $value, $expire);
    }

    /**
     * Обновляет время жизни кеша
     * @param $cache_key
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