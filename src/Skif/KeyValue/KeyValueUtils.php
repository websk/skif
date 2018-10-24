<?php

namespace Skif\KeyValue;

use Websk\Skif\CacheWrapper;
use Websk\Skif\DBWrapper;

/**
 * Class KeyValueUtils
 * @package Skif\KeyValue
 */
class KeyValueUtils
{

    /**
     * @return array
     * @throws \Exception
     */
    public static function getKeyValueIdsArr()
    {
        $key_value_ids_arr = DBWrapper::readColumn(
            "SELECT id FROM " . KeyValue::DB_TABLE_NAME . " ORDER BY name"
        );

        return $key_value_ids_arr;
    }

    /**
     * @param string $name
     * @param string $default_value
     * @return string
     * @throws \Exception
     */
    public static function getValueByName(string $name, string $default_value = '')
    {
        $cache_key = self::getValueByNameCacheKey($name);

        $cache = CacheWrapper::get($cache_key);

        if ($cache !== false) {
            return $cache;
        }

        $value = DBWrapper::readField(
            'SELECT value FROM ' . KeyValue::DB_TABLE_NAME . ' WHERE name = ?',
            array($name)
        );

        if ($value === false) {
            $value = $default_value;
        }

        CacheWrapper::set($cache_key, $value, 86400);

        return $value;
    }

    /**
     * @param string $name
     * @return string
     */
    public static function getValueByNameCacheKey(string $name)
    {
        return 'key_value_by_name_' .  $name;
    }
}
