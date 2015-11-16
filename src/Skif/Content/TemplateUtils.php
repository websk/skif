<?php

namespace Skif\Content;


class TemplateUtils
{

    public static function getTemplateIdByUrl($url)
    {

    }

    /**
     * Список ID шаблонов
     * @return array
     */
    public static function getTemplatesIdsArr()
    {
        $query = "SELECT id FROM " . \Skif\Content\Template::DB_TABLE_NAME;
        return \Skif\DB\DBWrapper::readColumn($query);
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