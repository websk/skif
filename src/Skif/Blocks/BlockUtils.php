<?php

namespace Skif\Blocks;

use WebSK\Utils\Network;
use WebSK\Utils\Filter;
use Websk\Slim\Container;
use Websk\Skif\CacheWrapper;
use Websk\Skif\DBWrapper;
use WebSK\Skif\Users\UsersServiceProvider;

/**
 * Class BlockUtils
 * @package Skif\Blocks
 */
class BlockUtils
{

    /**
     * Видимость блока для пользователя
     * @param int $block_id
     * @param int $user_id
     * @return bool
     */
    public static function blockIsVisibleByUserId(int $block_id, int $user_id)
    {
        $block_obj = Block::factory($block_id);

        // Проверяем блок на видимость для ролей
        $block_role_ids_arr = $block_obj->getRoleIdsArr();

        if (!$block_role_ids_arr) {
            return true; // виден всем
        }

        if (!$user_id) {
            return false;
        }

        $container = Container::self();
        $user_service = UsersServiceProvider::getUserService($container);

        $user_obj = $user_service->getById($user_id, false);
        if (!$user_obj) {
            return false;
        }

        foreach ($block_role_ids_arr as $role_id) {
            if (in_array($role_id, $user_service->getRoleIdsArrByUserId($user_id))) {
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
    public static function blockIsVisibleOnPage(int $block_id, string $page_url)
    {
        $block_obj = Block::factory($block_id);

        if ($block_obj->getPages()) {
            return self::checkBlockComplexVisibility($block_id, $page_url);
        }

        return false;
    }

    /**
     * @param int $block_id
     * @param string $real_path
     * @return bool
     */
    protected static function checkBlockComplexVisibility(int $block_id, string $real_path = '')
    {
        $block_obj = Block::factory($block_id);
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
                $filter_obj = new Filter($page_filter_str);

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
    public static function getContentByBlockId(int $block_id)
    {
        $block_obj = Block::factory($block_id);

        $cache_enabled = true;

        if ($block_obj->getCache() == Block::BLOCK_NO_CACHE) {
            $cache_enabled = false;
        }


        $cache_key = self::getBlockContentCacheKey($block_id);

        if ($cache_enabled) {
            $cached_content = CacheWrapper::get($cache_key);

            if ($cached_content !== false) {
                return $cached_content;
            }
        }

        $block_content = $block_obj->getBody();

        if ($block_obj->getFormat() == Block::BLOCK_FORMAT_TYPE_PHP) {
            $block_content = $block_obj->evalContentPHPBlock();
        }

        if ($cache_enabled) {
            CacheWrapper::set($cache_key, $block_content);
        }

        return $block_content;
    }

    /**
     * @param int $block_id
     * @return string|null
     */
    protected static function getBlockContentCacheKey($block_id)
    {
        $block_obj = Block::factory($block_id);

        $cid_parts = array('block_content');
        $cid_parts[] = $block_obj->getId();

        // Кешируем блоки по полному урлу $_SERVER['REQUEST_URI'], в т.ч. с $_GET параметрами.
        // Т.к. содержимое блока может различаться в зависимости от $_GET параметров.
        if ($block_obj->getCache() == Block::BLOCK_CACHE_PER_PAGE) {
            $cid_parts[] = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        if ($block_obj->getCache() == Block::BLOCK_CACHE_PER_USER) {
            $cid_parts[] = Network::getClientIpXff();
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
        $blocks_ids_arr = DBWrapper::readColumn(
            "SELECT id FROM " . Block::DB_TABLE_NAME . " WHERE template_id = ? ORDER BY page_region_id, weight, title",
            array($template_id)
        );

        return $blocks_ids_arr;
    }

    /**
     * Массив Block Id в регионе
     * @param int $page_region_id
     * @param int $template_id
     * @return array|bool|mixed
     * @throws \Exception
     */
    public static function getBlockIdsArrByPageRegionId(int $page_region_id, int $template_id)
    {
        $cache_key = self::getBlockIdsArrByPageRegionIdCacheKey($page_region_id, $template_id);

        $blocks_ids_arr = CacheWrapper::get($cache_key);
        if ($blocks_ids_arr !== false) {
            return $blocks_ids_arr;
        }

        $query = "SELECT id FROM " . Block::DB_TABLE_NAME . " WHERE page_region_id = ? AND template_id=? ORDER BY weight, title";

        $blocks_ids_arr = DBWrapper::readColumn(
            $query,
            array(
                $page_region_id,
                $template_id
            )
        );

        CacheWrapper::set($cache_key, $blocks_ids_arr, 3600);

        return $blocks_ids_arr;
    }

    /**
     * @param int $page_region_id
     * @param int $template_id
     */
    public static function clearBlockIdsArrByPageRegionIdCache(int $page_region_id, int $template_id)
    {
        $cache_key = self::getBlockIdsArrByPageRegionIdCacheKey($page_region_id, $template_id);
        CacheWrapper::delete($cache_key);
    }

    /**
     * @param int $page_region_id
     * @param int $template_id
     * @return string
     */
    protected static function getBlockIdsArrByPageRegionIdCacheKey(int $page_region_id, int $template_id)
    {
        $cache_key = 'template_id_' . $template_id . '_block_ids_arr_by_page_region_id_';

        if ($page_region_id == Block::BLOCK_REGION_NONE) {
            return $cache_key . 'disabled';
        }

        return $cache_key . $page_region_id;
    }

    /**
     * Массив возможных форматов блока
     * @return array
     */
    public static function getFormatsArr()
    {
        return array(
            Block::BLOCK_FORMAT_TYPE_PLAIN => 'Текст',
            Block::BLOCK_FORMAT_TYPE_HTML => 'HTML',
            Block::BLOCK_FORMAT_TYPE_PHP => 'PHP code'
        );
    }

    /**
     * Массив способов кеширования блока
     * @return array
     */
    public static function getCachesArr()
    {
        return array(
            Block::BLOCK_NO_CACHE => 'не кэшировать',
            Block::BLOCK_CACHE_PER_USER => 'кэшировать для каждого пользователя',
            Block::BLOCK_CACHE_PER_PAGE => 'кэшировать для каждого урла',
            Block::BLOCK_CACHE_GLOBAL => 'кэшировать глобально'
        );
    }
}
