<?php

namespace Skif\Content;


class TemplateUtils
{

    public static function getTemplateIdByUrl($url)
    {


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