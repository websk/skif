<?php

namespace Skif\Blocks;

class PageRegions
{
    static public function checkBlockComplexVisibility($block_std_obj, $real_path = '')
    {
        $pages = $block_std_obj->pages;

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
                        $visible = TRUE;
                    }

                    if ($filter_obj->is_negative) {
                        $visible = FALSE;
                    }
                }

            }
        }

        return $visible;
    }

    static function renderBlocksByRegion($region, $theme, $page_url = '')
    {
        $output = '';
        $list = self::block_list($region, $theme, $page_url);

        if ($list) {
            foreach ($list as $block_id => $block_std_obj) {
                $output .= '<!-- '. $block_std_obj->id .' -->';

                if (is_string($block_std_obj->content)) {
                    $output .= $block_std_obj->content;
                }

                $output .= '<!-- /'. $block_std_obj->id .' -->';

            }
        }

        return $output;
    }

    static function block_list($region, $theme_key, $page_url = '')
    {
        if ($page_url == '') {
            //$page_url = $_SERVER['REQUEST_URI'];

            // Берем url без $_GET параметров, т.к. это влияет на видимость блоков.
            // Блоки на странице page_example$ должны выводиться, например, и по адресу page_example?query=qwerty
            $page_url = \Skif\UrlManager::getUriNoQueryString();
        }

        $cache_key = self::getRegionBlocksCacheKey($theme_key, $region);
        $cached_value = \Skif\Cache\CacheWrapper::get($cache_key);
        if ($cached_value !== false){
            $blocks_objs_arr = $cached_value;
        } else {
            $blocks_objs_arr = \Skif\DB\DBWrapper::readObjects(
                "SELECT * FROM blocks WHERE theme = ? AND status = 1 AND region = ? ORDER BY region, weight",
                array($theme_key, $region)
            );

            \Skif\Cache\CacheWrapper::set($cache_key, $blocks_objs_arr, 10);
        }

        $cache_key = 'blocks_roles_all';
        $cached_value = \Skif\Cache\CacheWrapper::get($cache_key);
        if ($cached_value !== false){
            $roles_arr = $cached_value;
        } else {
            $roles_arr = \Skif\DB\DBWrapper::readObjects(
                "SELECT block_id, role_id FROM blocks_roles"
            );

            \Skif\Cache\CacheWrapper::set($cache_key, $roles_arr, 10);
        }

        $block_roles_arr = array();

        foreach ($roles_arr as $role) {
            $block_roles_arr[$role->block_id][] = $role->role_id;
        }


        $blocks_in_region_arr = array();

        foreach ($blocks_objs_arr as $block_std_obj) {

            // Проверяем блок на видимость для ролей
            if (array_key_exists($block_std_obj->id, $block_roles_arr)) {
                $user_id = \Skif\Users\AuthUtils::getCurrentUserId();
                if (!$user_id) {
                    continue;
                }

                $user_obj = \Skif\Users\User::factory($user_id, false);
                if ($user_obj) {
                    continue;
                }

                if (!in_array($block_roles_arr[$block_std_obj->id], $user_obj->getRolesIdsArr())) {
                    continue;
                }
            }

            $page_match = false;

            // Match path if necessary
            if ($block_std_obj->pages) {
                if ($block_std_obj->visibility == 3) {
                    $page_match = self::checkBlockComplexVisibility($block_std_obj, $page_url);
                }
            }

            if (!$page_match) {
                continue;
            }


            // oLog: store stats
            if (\Skif\Conf\ConfWrapper::value('profile_block_cache', 0)) {
                $_oLog_cache = \Skif\Cache\CacheFactory::getCacheObj();
                if ($_oLog_cache instanceof \Skif\Cache\Cache) {
                    $_oLog_memcache_obj = $_oLog_cache->getConnectionObj();
                    if ($_oLog_memcache_obj instanceof \Memcache) {
                        $_oLog_memcache_obj->add('_spb_drupal_block_display_' . $block_std_obj->id, 0);
                        $_oLog_memcache_obj->increment('_spb_drupal_block_display_' . $block_std_obj->id, 1);
                    } elseif ($_oLog_memcache_obj instanceof \Memcached) {
                        $_oLog_memcache_obj->add('_spb_drupal_block_display_' . $block_std_obj->id, 0);
                        $_oLog_memcache_obj->increment('_spb_drupal_block_display_' . $block_std_obj->id, 1);
                    }
                }
            }


            $is_get = ($_SERVER['REQUEST_METHOD'] == 'GET');
            $cid = self::_block_get_cache_id($block_std_obj);

            if ($is_get && $cid && ($cache = \Skif\Cache\CacheWrapper::get($cid))) {
                $content = $cache;
            } else {

                // oLog: profile
                $_oLog_gen_start = microtime(TRUE);

                $block_obj = \Skif\Blocks\BlockFactory::loadBlockObj($block_std_obj->id);
                \Skif\Utils::assert($block_obj);
                $content = $block_obj->renderBlockContent();


                // oLog: store stats
                if (\Skif\Conf\ConfWrapper::value('profile_block_cache', 0)) {
                    $_oLog_cache = \Skif\Cache\CacheFactory::getCacheObj();
                    if ($_oLog_cache instanceof \Skif\Cache\Cache) {
                        $_oLog_gen_end = microtime(TRUE);
                        $_oLog_gen_secs = $_oLog_gen_end - $_oLog_gen_start;
                        $_oLog_gen_msecs = intval($_oLog_gen_secs * 1000);
                        if ($_oLog_gen_msecs < 0) {
                            $_oLog_gen_msecs = 0;
                        }

                        $_oLog_memcache_obj = $_oLog_cache->getConnectionObj();
                        if ($_oLog_memcache_obj instanceof \Memcache) {
                            $_oLog_memcache_obj->add('_spb_drupal_block_gen_' . $block_std_obj->id, 0);
                            $_oLog_memcache_obj->increment('_spb_drupal_block_gen_' . $block_std_obj->id, 1);

                            $_oLog_memcache_obj->add('_spb_drupal_block_gen_msecs_' . $block_std_obj->id, 0);
                            $_oLog_memcache_obj->increment('_spb_drupal_block_gen_msecs_' . $block_std_obj->id, $_oLog_gen_msecs);
                        } elseif ($_oLog_memcache_obj instanceof \Memcached) {
                            $_oLog_memcache_obj->add('_spb_drupal_block_gen_' . $block_std_obj->id, 0);
                            $_oLog_memcache_obj->increment('_spb_drupal_block_gen_' . $block_std_obj->id, 1);

                            $_oLog_memcache_obj->add('_spb_drupal_block_gen_msecs_' . $block_std_obj->id, 0);
                            $_oLog_memcache_obj->increment('_spb_drupal_block_gen_msecs_' . $block_std_obj->id, $_oLog_gen_msecs);
                        }
                    }
                }

                if ($cid) {
                    $block_obj = \Skif\Blocks\BlockFactory::loadBlockObj($block_std_obj->id);
                    \Skif\Utils::assert($block_obj);

                    \Skif\Cache\CacheWrapper::set($cid, $content);
                }
            }

            if (isset($content)) {
                $block_std_obj->content = $content;
            }

            if (isset($block_std_obj->content) && $block_std_obj->content) {
                // Override default block title if a custom display title is present.
                if ($block_std_obj->title) {
                    // Check plain here to allow module generated titles to keep any markup.
                    $block_std_obj->subject = $block_std_obj->title == '<none>' ? '' : $block_std_obj->title;
                }
                if (!isset($block_std_obj->subject)) {
                    $block_std_obj->subject = '';
                }
                $blocks_in_region_arr[$block_std_obj->id] = $block_std_obj;
            }
        }

        return $blocks_in_region_arr;
    }

    static public function _block_get_cache_id($block)
    {
        //global $theme, $base_root, $user;

        // User 1 being out of the regular 'roles define permissions' schema,
        // it brings too many chances of having unwanted output get in the cache
        // and later be served to other users. We therefore exclude user 1 from
        // block caching.

        if ($block->cache == \Skif\Constants::BLOCK_NO_CACHE) {
            return null;
        }

        $cid_parts = array();

        // Start with common sub-patterns: block identification, theme, language.
        $cid_parts[] = $block->id;

        // Кешируем блоки по полному урлу $_SERVER['REQUEST_URI'], в т.ч. с $_GET параметрами.
        // Т.к. содержимое блока может различаться. Например, страница телепрограммы по дням.
        if ($block->cache & \Skif\Constants::BLOCK_CACHE_PER_PAGE) {
            $cid_parts[] = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        return implode(':', $cid_parts);
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

    public static function getRegionBlocksCacheKey($theme_key, $region)
    {
        return \Skif\Conf\ConfWrapper::value('site_url') . '_region_blocks_' . $theme_key . '_' . $region;
    }
}