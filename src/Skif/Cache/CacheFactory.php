<?php

namespace Skif\Cache;

class CacheFactory {
    /**
     * @return Cache
     */
    static public function getCacheObj()
    {
        static $cache_obj;

        if (isset($cache_obj)) {
            return $cache_obj;
        }

        $cache_obj = new \Skif\Cache\Cache();
        return $cache_obj;
    }
}