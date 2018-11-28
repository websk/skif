<?php

namespace WebSK\Skif\Content;

use Websk\Cache\CacheWrapper;
use Websk\Skif\DBWrapper;

/**
 * Class TemplateUtils
 * @package WebSK\Skif\Content
 */
class TemplateUtils
{

    public static function getTemplateIdByName($name)
    {
        $cache_key = self::getTemplateIdByNameCacheKey($name);

        $cache = CacheWrapper::get($cache_key);
        if ($cache !== false) {
            return $cache;
        }

        $query = "SELECT id FROM " . Template::DB_TABLE_NAME . " WHERE name=?";

        $template_id = DBWrapper::readField($query, array($name));

        CacheWrapper::set($cache_key, $template_id, 3600);

        return $template_id;
    }

    public static function getTemplateIdByNameCacheKey($name)
    {
        return 'template_id_by_name_' . $name;
    }

    /**
     * Список ID шаблонов
     * @return array
     */
    public static function getTemplatesIdsArr()
    {
        $query = "SELECT id FROM " . Template::DB_TABLE_NAME;

        return DBWrapper::readColumn($query);
    }

    public static function getLayoutFileByTemplateId($template_id)
    {
        $template_obj = Template::factory($template_id, false);
        if (!$template_obj) {
            return 'layouts/layout.main.tpl.php';
        }

        return $template_obj->getLayoutTemplateFilePath();
    }
}
