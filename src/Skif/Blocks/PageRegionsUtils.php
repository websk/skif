<?php

namespace Skif\Blocks;


class PageRegionsUtils
{
    /**
     * Вывод блоков региона в теме
     * @param string $page_region_name
     * @param string $template_name
     * @param string $page_url
     * @return string
     */
    public static function renderBlocksByPageRegionNameAndTemplateName($page_region_name, $template_name, $page_url = '')
    {
        $output = '';

        $template_id = \Skif\Content\TemplateUtils::getTemplateIdByName($template_name);
        $page_region_id = self::getPageRegionIdByNameAndTemplateId($page_region_name, $template_id);

        $blocks_ids_arr = self::getVisibleBlocksIdsArrByRegionId($page_region_id, $template_id, $page_url);

        foreach ($blocks_ids_arr as $block_id) {
            $output .= \Skif\PhpTemplate::renderTemplateBySkifModule(
                'Blocks',
                'block.tpl.php',
                array(
                    'block_id' => $block_id
                )
            );
        }

        return $output;
    }

    public static function getPageRegionIdByNameAndTemplateId($name, $template_id)
    {
        $cache_key = self::getPageRegionIdByNameAndTemplateIdCacheKey($name, $template_id);

        $cache = \Websk\Skif\CacheWrapper::get($cache_key);
        if ($cache !== false) {
            return $cache;
        }

        $query = "SELECT id FROM " . \Skif\Blocks\PageRegion::DB_TABLE_NAME . " WHERE name=? AND template_id=?";

        $page_region_id = \Websk\Skif\DBWrapper::readField($query, array($name, $template_id));

        \Websk\Skif\CacheWrapper::set($cache_key, $page_region_id, 3600);

        return $page_region_id;
    }

    public static function getPageRegionIdByNameAndTemplateIdCacheKey($name, $template_id)
    {
        return 'page_region_id_by_name_' . $name . '_and_template_id' . $template_id;
    }

    /**
     * Массив Id видимых блоков региона в теме
     * @param $page_region_id
     * @param $template_id
     * @param string $page_url
     * @return array
     */
    protected static function getVisibleBlocksIdsArrByRegionId($page_region_id, $template_id, $page_url = '')
    {
        if ($page_url == '') {
            // Берем url без $_GET параметров, т.к. это влияет на видимость блоков.
            // Блоки на странице Vidy_sporta/Avtosport$ должны выводиться, например, и по адресу Vidy_sporta/Avtosport
            $page_url = \Skif\UrlManager::getUriNoQueryString();
        }

        $blocks_ids_arr = \Skif\Blocks\BlockUtils::getBlockIdsArrByPageRegionId($page_region_id, $template_id);

        $visible_blocks_ids_arr = array();

        $current_user_id = \WebSK\Skif\Users\AuthUtils::getCurrentUserId();

        foreach ($blocks_ids_arr as $block_id) {
            if (!\Skif\Blocks\BlockUtils::blockIsVisibleByUserId($block_id, $current_user_id)) {
                continue;
            }

            if (!\Skif\Blocks\BlockUtils::blockIsVisibleOnPage($block_id, $page_url)) {
                continue;
            }

            $visible_blocks_ids_arr[] = $block_id;
        }

        return $visible_blocks_ids_arr;
    }

    /**
     * Массив PageRegionId для темы
     * @param $template_id
     * @return mixed
     */
    public static function getPageRegionIdsArrByTemplateId($template_id)
    {
        static $static_page_region_ids_arr = array();

        if (!array_key_exists($template_id, $static_page_region_ids_arr)) {
            $query = "SELECT id FROM " . \Skif\Blocks\PageRegion::DB_TABLE_NAME . " WHERE template_id = ?";

            $page_region_ids_arr = \Websk\Skif\DBWrapper::readColumn($query, array($template_id));
        }

        $page_region_ids_arr[] = \Skif\Blocks\Block::BLOCK_REGION_NONE;

        $static_page_region_ids_arr[$template_id] = $page_region_ids_arr;

        return $page_region_ids_arr;
    }

}