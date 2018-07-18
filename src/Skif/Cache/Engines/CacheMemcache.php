<?php

namespace Skif\Cache\Engines;

use Skif\Conf\ConfWrapper;
use Skif\Utils;

class CacheMemcache implements CacheEngineInterface
{
    /**
     * @param $key
     * @param $value
     * @param $exp
     * @return bool
     * @throws \Exception
     */
    public static function set($key, $value, $exp)
    {
        if ($exp == -1) {
            $exp = 60;
        }

        if ($exp > 0) {
            if ($exp > 2592000) { // не добавляем тайм для мелких значений, чтобы не добавлять сложностей с разными часами на серверах и т.п.
                $exp += time();
            }
        } else {
            $exp = 0;
        }

        if ($exp == 0) {
            return true;
        }

        $mc = self::getMcConnectionObj(); // do not check result - already checked
        if (!$mc) {
            return false;
        }

        $full_key = self::getKey($key);

        $mcs_result = $mc->set($full_key, $value, MEMCACHE_COMPRESSED, $exp);

        if (!$mcs_result) {
            return false;
        }

        return true;
    }

    /**
     * @param $key
     * @return bool
     * @throws \Exception
     */
    public static function increment($key)
    {
        $mc = self::getMcConnectionObj();
        if (!$mc) {
            return false;
        }

        $full_key = self::getKey($key);
        if (!$mc->increment($full_key)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $key
     * @return array|bool|string
     * @throws \Exception
     */
    public static function get($key)
    {
        $mc = self::getMcConnectionObj();
        if (!$mc) {
            return false;
        }

        $full_key = self::getKey($key);
        $result = $mc->get($full_key);

        return $result;
    }

    /**
     * @param $key
     * @return bool
     * @throws \Exception
     */
    public static function delete($key)
    {
        $mc = self::getMcConnectionObj();
        if (!$mc) {
            return false;
        }

        $full_key = self::getKey($key);
        return $mc->delete($full_key);
    }

    /**
     * @return \Memcache|null
     * @throws \Exception
     */
    public static function getMcConnectionObj()
    {
        static $memcache = null;

        if (isset($memcache)) {
            return $memcache;
        }

        $memcache_servers = ConfWrapper::value('cache.servers');

        if (!$memcache_servers) {
            return null;
        }

        /** @var \Memcache $memcache */
        $memcache = new \Memcache;

        foreach ($memcache_servers as $server) {
            Utils::assert($memcache->addServer($server['host'], $server['port']));
            $memcache->setCompressThreshold(5000, 0.2);
        }

        return $memcache;
    }

    /**
     * @param $key
     * @return string
     */
    public static function getKey($key)
    {
        $prefix = ConfWrapper::value('cache.key_prefix');
        if ($prefix == '') {
            $prefix = 'default';
        }

        $full_key = $prefix . '-' . $key;

        return md5($full_key);
    }
} 