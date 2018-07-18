<?php

namespace Skif\Cache\Engines;

use Skif\Conf\ConfWrapper;

class CacheRedis implements CacheEngineInterface
{
    /**
     * @param $key
     * @param $value
     * @param $ttl_secs
     * @return bool
     */
    public static function set($key, $value, $ttl_secs)
    {
        if ($ttl_secs == -1) {
            $ttl_secs = 60;
        }

        if ($ttl_secs < 0) {
            $ttl_secs = 0;
        }

        if ($ttl_secs == 0) {
            return true;
        }

        $redis_connection_obj = self::getRedisConnectionObj(); // do not check result - already checked
        if (!$redis_connection_obj) {
            return false;
        }

        $full_key = self::getKey($key);
        $value_ser = serialize($value);

        if ($ttl_secs > 0) {
            $mcs_result = $redis_connection_obj->setex($full_key, $ttl_secs, $value_ser);
        } else {
            $mcs_result = $redis_connection_obj->set($full_key, $value_ser);
        }

        if (!$mcs_result) {
            return false;
        }

        return true;
    }

    /**
     * @param $key
     * @throws \Exception
     */
    public static function increment($key)
    {
        throw new \Exception('redis increment not implemented');
    }

    /**
     * returns false if key not found
     * @param $key
     * @return array|bool|string
     */
    public static function get($key)
    {
        $redis_connection_obj = self::getRedisConnectionObj();
        if (!$redis_connection_obj) {
            return false;
        }

        $full_key = self::getKey($key);
        $result = $redis_connection_obj->get($full_key);

        if ($result === false) {
            return false;
        }

        $result = unserialize($result);

        return $result;
    }

    public static function delete($key)
    {
        $redis_connection_obj = self::getRedisConnectionObj();
        if (!$redis_connection_obj) {
            return false;
        }

        $full_key = self::getKey($key);
        return $redis_connection_obj->del([$full_key]);
    }

    /**
     * @return null|\Predis\Client
     */
    public static function getRedisConnectionObj()
    {
        static $redis = null;

        if (isset($redis)) {
            return $redis;
        }

        $redis_servers = ConfWrapper::value('cache.servers');
        if (!$redis_servers) {
            return null;
        }

        $servers_arr = [];
        foreach ($redis_servers as $server) {
            $servers_arr[] = [
                'scheme' => 'tcp',
                'host' => $server['host'],
                'port' => $server['port']
            ];
        }

        $redis = new \Predis\Client($servers_arr);

        return $redis;
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