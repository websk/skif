<?php

namespace Skif\Cache;

use Skif\Cache\Engines\CacheEngineInterface;
use Skif\Cache\Engines\CacheMemcache;
use Skif\Cache\Engines\CacheRedis;
use Skif\Conf\ConfWrapper;

class CacheFactory
{
    protected static $cache_obj = null;

    /**
     * @return CacheEngineInterface
     */
    public static function getCacheObj()
    {
        if (!empty(self::$cache_obj)) {
            return self::$cache_obj;
        }

        $engine = ConfWrapper::value('cache.engine', 'memcache');

        if ($engine == 'memcache') {
            self::$cache_obj = new CacheMemcache();
        } elseif ($engine == 'redis') {
            self::$cache_obj = new CacheRedis();
        } else {
            throw new \Exception('Cache engine failed');
        }

        return self::$cache_obj;
    }
}