<?php

namespace Skif\Blocks;

class BlockUtils
{

    /**
     * Видимость блока для пользователя
     * @param $block_id
     * @param $user_id
     * @return bool
     */
    public static function blockIsVisibleByUserId($block_id, $user_id)
    {
        $block_obj = \Skif\Blocks\Block::factory($block_id);

        // Проверяем блок на видимость для ролей
        $block_role_ids_arr = $block_obj->getRoleIdsArr();

        if (!$block_role_ids_arr) {
            return true; // виден всем
        }

        if (!$user_id) {
            return false;
        }

        $user_obj = \Skif\Users\User::factory($user_id, false);
        if (!$user_obj) {
            return false;
        }

        foreach ($block_role_ids_arr as $rubric_id) {
            if (in_array($rubric_id, $user_obj->getRolesIdsArr())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Видимость блока на странице
     * @param int $block_id
     * @param string $page_url
     * @return bool
     */
    public static function blockIsVisibleOnPage($block_id, $page_url)
    {
        $block_obj = \Skif\Blocks\Block::factory($block_id);

        if ($block_obj->getPages()) {
            return self::checkBlockComplexVisibility($block_id, $page_url);
        }

        return false;
    }

    protected static function checkBlockComplexVisibility($block_id, $real_path = '')
    {
        $block_obj = \Skif\Blocks\Block::factory($block_id);
        $pages = $block_obj->getPages();

        // parse pages

        $pages = str_replace("\r", "\n", $pages);
        $pages = str_replace("\n\n", "\n", $pages);

        $pages_arr = explode("\n", $pages);

        if (count($pages_arr) == 0) {
            return false;
        }

        // check pages

        $visible = false;

        foreach ($pages_arr as $page_filter_str) {
            $page_filter_str = trim($page_filter_str);

            if (strlen($page_filter_str) > 2) {
                // convert filter string to object
                $filter_obj = \Skif\Util\FilterFactory::getFilter($page_filter_str);

                if ($filter_obj->matchesPage($real_path)) {
                    if ($filter_obj->is_positive) {
                        $visible = true;
                    }

                    if ($filter_obj->is_negative) {
                        $visible = false;
                    }
                }

            }
        }

        return $visible;
    }

    /**
     * Содержимое блока
     * @param int $block_id
     * @return string
     */
    public static function getContentByBlockId($block_id)
    {
        $block_obj = \Skif\Blocks\Block::factory($block_id);

        $cache_enabled = true;

        if ($block_obj->getCache() == \Skif\Blocks\Block::BLOCK_NO_CACHE) {
            $cache_enabled = false;
        }


        $cache_key = \Skif\Blocks\BlockUtils::getBlockContentCacheKey($block_id);

        if ($cache_enabled) {
            $cached_content = \Skif\Cache\CacheWrapper::get($cache_key);

            if ($cached_content !== false) {
                return $cached_content;
            }
        }

        $block_content = $block_obj->getBody();

        if ($block_obj->getFormat() == \Skif\Blocks\Block::BLOCK_FORMAT_TYPE_PHP) {
            $block_content = $block_obj->evalContentPHPBlock();
        }

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
        // Т.к. содержимое блока может различаться в зависимости от $_GET параметров.
        if ($block_obj->getCache() == \Skif\Blocks\Block::BLOCK_CACHE_PER_PAGE) {
            $cid_parts[] = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        if ($block_obj->getCache() == \Skif\Blocks\Block::BLOCK_CACHE_PER_USER) {
            $cid_parts[] = \Skif\Network::getClientIpXff();
        }

        return implode(':', $cid_parts);
    }


    /**
     * Массив Block Id в теме
     * @param string $template_id
     * @return array
     */
    public static function getBlockIdsArrByTemplateId($template_id)
    {
        $blocks_ids_arr = \Skif\DB\DBWrapper::readColumn(
            "SELECT id FROM " . \Skif\Blocks\Block::DB_TABLE_NAME . " WHERE template_id = ? ORDER BY page_region_id, weight, title",
            array($template_id)
        );

        return $blocks_ids_arr;
    }

    /**
     * Массив Block Id в регионе
     * @param string $page_region_id
     * @return array
     */
    public static function getBlockIdsArrByPageRegionId($page_region_id)
    {
        $cache_key = \Skif\Blocks\BlockUtils::getBlockIdsArrByPageRegionIdCacheKey($page_region_id);

        $blocks_ids_arr = \Skif\Cache\CacheWrapper::get($cache_key);
        if ($blocks_ids_arr !== false) {
            return $blocks_ids_arr;
        }

        $query = "SELECT id FROM " . \Skif\Blocks\Block::DB_TABLE_NAME . " WHERE page_region_id = ? ORDER BY weight, title";

        $blocks_ids_arr = \Skif\DB\DBWrapper::readColumn(
            $query,
            array($page_region_id)
        );

        \Skif\Cache\CacheWrapper::set($cache_key, $blocks_ids_arr, 3600);

        return $blocks_ids_arr;
    }

    public static function clearBlockIdsArrByPageRegionIdCache($region)
    {
        $cache_key = \Skif\Blocks\BlockUtils::getBlockIdsArrByPageRegionIdCacheKey($region);
        \Skif\Cache\CacheWrapper::delete($cache_key);
    }

    public static function getBlockIdsArrByPageRegionIdCacheKey($page_region_id)
    {
        if ($page_region_id == \Skif\Blocks\Block::BLOCK_REGION_NONE) {
            return 'block_ids_arr_by_page_region_id_disabled';
        }

        return 'block_ids_arr_by_page_region_id_' . $page_region_id;
    }

    /**
     * Массив возможных форматов блока
     * @return array
     */
    public static function getFormatsArr()
    {
        return array(
            \Skif\Blocks\Block::BLOCK_FORMAT_TYPE_PLAIN => 'Текст',
            \Skif\Blocks\Block::BLOCK_FORMAT_TYPE_HTML => 'HTML',
            \Skif\Blocks\Block::BLOCK_FORMAT_TYPE_PHP => 'PHP code'
        );
    }

    /**
     * Массив способов кеширования блока
     * @return array
     */
    public static function getCachesArr()
    {
        return array(
            \Skif\Blocks\Block::BLOCK_NO_CACHE => 'не кэшировать',
            \Skif\Blocks\Block::BLOCK_CACHE_PER_USER => 'кэшировать для каждого пользователя',
            \Skif\Blocks\Block::BLOCK_CACHE_PER_PAGE => 'кэшировать для каждого урла',
            \Skif\Blocks\Block::BLOCK_CACHE_GLOBAL => 'кэшировать глобально'
        );
    }
}
