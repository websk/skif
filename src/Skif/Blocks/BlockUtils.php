<?php

namespace Skif\Blocks;

class BlockUtils
{

    /**
     * @param int $block_id
     * @return string
     */
    public static function getContentByBlockId($block_id)
    {
        $block_obj = \Skif\Blocks\Block::factory($block_id);

        $cache_key = \Skif\Blocks\BlockUtils::getBlockContentCacheKey($block_id);

        $cache_enabled = true;

        if ($block_obj->getCache() == \Skif\Constants::BLOCK_NO_CACHE) {
            $cache_enabled = false;
        }

        if ($cache_enabled) {
            $cached_content = \Skif\Cache\CacheWrapper::get($cache_key);

            if ($cached_content !== false) {
                return $cached_content;
            }
        }

        $block_content = $block_obj->renderBlockContent();

        if ($cache_enabled) {
            \Skif\Cache\CacheWrapper::set($cache_key, $block_content);
        }

        return $block_content;
    }

    /**
     * @param int $block_id
     * @return string|null
     */
    protected static function getBlockContentCacheKey($block_id)
    {
        $block_obj = \Skif\Blocks\Block::factory($block_id);

        $cid_parts = array('block_content');
        $cid_parts[] = $block_obj->getId();

        // Кешируем блоки по полному урлу $_SERVER['REQUEST_URI'], в т.ч. с $_GET параметрами.
        // Т.к. содержимое блока может различаться. Например, страница телепрограммы по дням.
        if ($block_obj->getCache() & \Skif\Constants::BLOCK_CACHE_PER_PAGE) {
            $cid_parts[] = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        return implode(':', $cid_parts);
    }

    /**
     * @return bool
     */
    public static function currentUserHasAccessToBlocksForAdministrators()
    {
        return \Skif\Users\AuthUtils::currentUserIsAdmin();
    }
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
