<?php

namespace Skif\Blocks;

class PageRegions
{
    public static function checkBlockComplexVisibility($block_id, $real_path = '')
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
     * @param string $region
     * @param string $theme
     * @param string $page_url
     * @return string
     */
    static function renderBlocksByRegion($region, $theme, $page_url = '')
    {
        $output = '';

        $blocks_ids_arr = self::getVisibleBlocksIdsArr($region, $theme, $page_url);

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

    /**
     * @param string $region
     * @param string $theme
     * @param string $page_url
     * @return array
     */
    static function getVisibleBlocksIdsArr($region, $theme, $page_url = '')
    {
        if ($page_url == '') {
            // Берем url без $_GET параметров, т.к. это влияет на видимость блоков.
            // Блоки на странице Vidy_sporta/Avtosport$ должны выводиться, например, и по адресу Vidy_sporta/Avtosport
            $page_url = \Skif\UrlManager::getUriNoQueryString();
        }

        $blocks_ids_arr = \Skif\Blocks\BlockUtils::getBlocksIdsArrInRegion($region, $theme);

        $visible_blocks_ids_arr = array();

        $has_access_to_blocks_for_administrators = \Skif\Blocks\BlockUtils::currentUserHasAccessToBlocksForAdministrators();

        foreach ($blocks_ids_arr as $block_id) {
            if (!self::blockIsVisibleOnPage($block_id, $page_url, $has_access_to_blocks_for_administrators)) {
                continue;
            }
            $visible_blocks_ids_arr[] = $block_id;
        }

        return $visible_blocks_ids_arr;
    }

    /**
     * @param int $block_id
     * @param string $page_url
     * @param $has_access_to_blocks_for_administrators
     * @return bool
     */
    public static function blockIsVisibleOnPage($block_id, $page_url, $has_access_to_blocks_for_administrators = false)
    {
        $block_obj = \Skif\Blocks\Block::factory($block_id);

        // Проверяем блок на видимость только для администраторов
        if (!$has_access_to_blocks_for_administrators && $block_obj->isVisibleOnlyForAdministrators()) {
            return false;
        }

        // Match path if necessary
        if ($block_obj->getPages()) {
            return self::checkBlockComplexVisibility($block_id, $page_url);
        }

        return false;
    }

    /**
     * Массив регионов для темы
     * @param $theme_key
     * @return mixed
     */
    public static function getRegionsArrByTheme($theme_key)
    {
        static $regions_arr = array();

        if (!array_key_exists($theme_key, $regions_arr)) {
            $query = "SELECT name, title FROM page_regions WHERE theme = ?";
            $regions_by_theme_arr = \Skif\DB\DBWrapper::readObjects($query, array($theme_key));
            foreach ($regions_by_theme_arr as $region_std_obj) {
                $regions_arr[$theme_key][$region_std_obj->name] = $region_std_obj->title;
            }
        }

        return $regions_arr[$theme_key];
    }
}