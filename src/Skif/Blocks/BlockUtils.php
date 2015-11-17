<?php

namespace Skif\Blocks;

class BlockUtils
{

    /**
     * @param string $theme
     * @return array
     */
    public static function getBlocksIdsArrByTheme($theme)
    {
        $blocks_ids_arr = \Skif\DB\DBWrapper::readColumn(
            "SELECT id FROM blocks
            WHERE theme = ?
            ORDER BY region, weight, info",
            array(
                $theme
            )
        );

        return $blocks_ids_arr;
    }

    /**
     * @param string $region
     * @param string $theme
     * @return array
     */
    public static function getBlocksIdsArrInRegion($region, $theme)
    {
        $cache_key = \Skif\Blocks\BlockUtils::getBlocksIdsArrInRegionCacheKey($region, $theme);

        $blocks_ids_arr = \Skif\Cache\CacheWrapper::get($cache_key);
        if ($blocks_ids_arr !== false) {
            return $blocks_ids_arr;
        }

        if ($region == \Skif\Constants::BLOCK_REGION_NONE) {
            $region = '';
        }

        $blocks_ids_arr = \Skif\DB\DBWrapper::readColumn(
            "SELECT id FROM blocks
            WHERE region = ? AND theme = ?
            ORDER BY weight, info",
            array(
                $region, $theme
            )
        );
        \Skif\Cache\CacheWrapper::set($cache_key, $blocks_ids_arr, 3600);

        return $blocks_ids_arr;
    }

    public static function clearBlocksIdsArrInRegionCache($region, $theme)
    {
        $cache_key = \Skif\Blocks\BlockUtils::getBlocksIdsArrInRegionCacheKey($region, $theme);
        \Skif\Cache\CacheWrapper::delete($cache_key);
    }

    public static function getBlocksIdsArrInRegionCacheKey($region, $theme)
    {
        if ($region == \Skif\Constants::BLOCK_REGION_NONE) {
            $region = '';
        }

        return 'blocks_in_region_' . $theme . '_' . $region;
    }

    public static function getFormatsArr()
    {
        return array(
            3 => 'Текст',
            4 => 'HTML',
            5 => 'PHP code'
        );
    }

    public static function getCachesArr()
    {
        return array(
            \Skif\Constants::BLOCK_REGION_NONE => 'не кэшировать',
            1 => 'кэшировать для каждой роли',
            2 => 'кэшировать для каждого пользователя',
            4 => 'кэшировать для каждого урла',
            8 => 'кэшировать глобально'
        );
    }
}
