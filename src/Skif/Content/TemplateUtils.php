<?php

namespace Skif\Content;


class TemplateUtils
{

    public static function getTemplateIdByName($name)
    {
        $cache_key = self::getTemplateIdByNameCacheKey($name);

        $cache = \Websk\Skif\CacheWrapper::get($cache_key);
        if ($cache !== false) {
            return $cache;
        }

        $query = "SELECT id FROM " . \Skif\Content\Template::DB_TABLE_NAME . " WHERE name=?";

        $template_id = \Websk\Skif\DBWrapper::readField($query, array($name));

        \Websk\Skif\CacheWrapper::set($cache_key, $template_id, 3600);

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
        $query = "SELECT id FROM " . \Skif\Content\Template::DB_TABLE_NAME;
        return \Websk\Skif\DBWrapper::readColumn($query);
    }

    public static function getLayoutFileByTemplateId($template_id)
    {
        $template_obj = \Skif\Content\Template::factory($template_id, false);
        if (!$template_obj) {
            return 'layouts/layout.main.tpl.php';
        }

        return $template_obj->getLayoutTemplateFilePath();
    }
}