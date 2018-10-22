<?php

namespace Skif\Cache;

use WebSK\Cache\CacheService;
use Websk\Skif\Container;
use WebSK\Skif\SkifApp;

class CacheWrapper
{
    protected static $storage_arr = [];

    /**
     * @param $key
     * @return bool|mixed
     */
    public static function get($key)
    {
        $container = Container::self();

        /** @var CacheService $cache_service */
        $cache_service = $container->get(SkifApp::SKIF_CACHE_SERVICE);

        return $cache_service->get($key);
    }

    /**
     * @param $key
     * @return bool
     */
    public static function delete($key)
    {
        $container = Container::self();

        /** @var CacheService $cache_service */
        $cache_service = $container->get(SkifApp::SKIF_CACHE_SERVICE);

        return $cache_service->delete($key);
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
        $container = Container::self();

        /** @var CacheService $cache_service */
        $cache_service = $container->get(SkifApp::SKIF_CACHE_SERVICE);

        return $cache_service->set($key, $value, $expire);
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