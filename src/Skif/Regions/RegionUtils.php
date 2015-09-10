<?php

namespace Skif\Regions;

class RegionUtils
{

    /**
     * ID региона по ID региона VK
     * @param $vk_id
     * @return int
     */
    public static function getRegionIdByVkId($vk_id)
    {
        $region_id = \Skif\DB\DBWrapper::readField("SELECT id FROM " . \Skif\Regions\Region::DB_TABLE_NAME . " WHERE vk_id=?", array($vk_id));
        if ($region_id === false) {
            $region_id = 0;
        }

        return $region_id;
    }

    /**
     * Список регионов
     * @return array
     * @throws \Exception
     */
    public static function getRegionsIdsArr()
    {
        $cache_key = 'regions_ids_arr';

        $cache = \Skif\Cache\CacheWrapper::get($cache_key);

        if ($cache !== false) {
            return $cache;
        }

        $regions_ids_arr = \Skif\DB\DBWrapper::readColumn("SELECT id FROM " . \Skif\Regions\Region::DB_TABLE_NAME . " ORDER BY title");

        \Skif\Cache\CacheWrapper::set($cache_key, $regions_ids_arr);

        return $regions_ids_arr;
    }
}