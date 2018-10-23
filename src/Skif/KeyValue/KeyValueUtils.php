<?php

namespace Skif\KeyValue;


class KeyValueUtils
{

    public static function getKeyValueIdsArr()
    {
        $key_value_ids_arr = \Websk\Skif\DBWrapper::readColumn(
            "SELECT id FROM " . \Skif\KeyValue\KeyValue::DB_TABLE_NAME . " ORDER BY name"
        );

        return $key_value_ids_arr;
    }

    /**
     * @param $name
     * @param string $default_value
     * @return string
     * @throws \Exception
     */
    public static function getValueByName($name, $default_value = '')
    {
        $cache_key = self::getValueByNameCacheKey($name);

        $cache = \Websk\Skif\CacheWrapper::get($cache_key);

        if ($cache !== false) {
            return $cache;
        }

        $value = \Websk\Skif\DBWrapper::readField(
            'SELECT value FROM ' . \Skif\KeyValue\KeyValue::DB_TABLE_NAME . ' WHERE name = ?',
            array($name)
        );

        if ($value === false) {
            $value = $default_value;
        }

        \Websk\Skif\CacheWrapper::set($cache_key, $value, 86400);

        return $value;
    }

    public static function getValueByNameCacheKey($name)
    {
        return 'key_value_by_name_' .  $name;
    }

}