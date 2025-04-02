<?php

namespace WebSK\Skif\Blocks;

use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Cache\CacheWrapper;
use WebSK\Auth\Auth;
use WebSK\DB\DBWrapper;
use WebSK\Slim\Container;
use WebSK\Utils\Url;
use WebSK\Views\PhpRender;

/**
 * Class PageRegionsUtils
 * @package WebSK\Skif\Blocks
 */
class PageRegionsUtils
{
    /**
     * @param string $page_region_name
     * @param string $template_name
     * @param string $page_url
     * @return string
     * @throws \Exception
     */
    public static function renderBlocksByPageRegionNameAndTemplateName(
        string $page_region_name,
        string $template_name,
        string $page_url = ''
    ): string {
        $output = '';

        $template_service = ContentServiceProvider::getTemplateService(Container::self());

        $template_id = $template_service->getIdByName($template_name);
        $page_region_id = self::getPageRegionIdByNameAndTemplateId($page_region_name, $template_id);

        $blocks_ids_arr = self::getVisibleBlocksIdsArrByRegionId($page_region_id, $template_id, $page_url);

        foreach ($blocks_ids_arr as $block_id) {
            $output .= PhpRender::renderTemplateInViewsDir(
                'block.tpl.php',
                array(
                    'block_id' => $block_id
                )
            );
        }

        return $output;
    }

    /**
     * @param string $name
     * @param int $template_id
     * @return bool|false|mixed
     * @throws \Exception
     */
    public static function getPageRegionIdByNameAndTemplateId(string $name, int $template_id): int
    {
        $cache_key = self::getPageRegionIdByNameAndTemplateIdCacheKey($name, $template_id);

        $cache = CacheWrapper::get($cache_key);
        if ($cache !== false) {
            return (int)$cache;
        }

        $query = "SELECT id FROM " . PageRegion::DB_TABLE_NAME . " WHERE name=? AND template_id=?";

        $page_region_id = (int)DBWrapper::readField($query, array($name, $template_id));

        CacheWrapper::set($cache_key, $page_region_id, 3600);

        return $page_region_id;
    }

    /**
     * @param string $name
     * @param int $template_id
     * @return string
     */
    public static function getPageRegionIdByNameAndTemplateIdCacheKey(string $name, int $template_id)
    {
        return 'page_region_id_by_name_' . $name . '_and_template_id' . $template_id;
    }

    /**
     * Массив Id видимых блоков региона в теме
     * @param int $page_region_id
     * @param int $template_id
     * @param string $page_url
     * @return array
     * @throws \Exception
     */
    protected static function getVisibleBlocksIdsArrByRegionId(
        int $page_region_id,
        int $template_id,
        string $page_url = ''
    ): array {
        if ($page_url == '') {
            // Берем url без $_GET параметров, т.к. это влияет на видимость блоков.
            // Блоки на странице Vidy_sporta/Avtosport$ должны выводиться, например, и по адресу Vidy_sporta/Avtosport
            $page_url = Url::getUriNoQueryString();
        }

        $blocks_ids_arr = BlockUtils::getBlockIdsArrByPageRegionId($page_region_id, $template_id);

        $visible_blocks_ids_arr = [];

        $current_user_id = Auth::getCurrentUserId();

        foreach ($blocks_ids_arr as $block_id) {
            if (!BlockUtils::blockIsVisibleByUserId($block_id, $current_user_id)) {
                continue;
            }

            if (!BlockUtils::blockIsVisibleOnPage($block_id, $page_url)) {
                continue;
            }

            $visible_blocks_ids_arr[] = $block_id;
        }

        return $visible_blocks_ids_arr;
    }

    /**
     * Массив PageRegionId для темы
     * @param int $template_id
     * @return array
     */
    public static function getPageRegionIdsArrByTemplateId(int $template_id): array
    {
        static $static_page_region_ids_arr = [];

        $page_region_ids_arr = [];

        if (!array_key_exists($template_id, $static_page_region_ids_arr)) {
            $query = "SELECT id FROM " . PageRegion::DB_TABLE_NAME . " WHERE template_id = ?";

            $page_region_ids_arr = DBWrapper::readColumn($query, [$template_id]);
        }

        $page_region_ids_arr[] = PageRegion::BLOCK_REGION_NONE;

        $static_page_region_ids_arr[$template_id] = $page_region_ids_arr;

        return $page_region_ids_arr;
    }
}
