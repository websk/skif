<?php

namespace Skif\SiteMenu;


class SiteMenuRender
{
    public static function renderSiteMenu($site_menu_id, $template = '')
    {
        if (!$template) {
            $template_file = 'site_menu_' . $site_menu_id . '.tpl.php';

            if (!\Skif\PhpTemplate::existsTemplateBySkifModuleRelativeToRootSitePath('SiteMenu', $template_file)) {
                $template_file = 'site_menu_default.tpl.php';
            }

            return \Skif\PhpTemplate::renderTemplateBySkifModule(
                'SiteMenu',
                $template_file,
                array('site_menu_id' => $site_menu_id)
            );
        }

        return \Skif\PhpTemplate::renderTemplate($template, array('site_menu_id' => $site_menu_id));
    }

    public static function renderSiteSubMenu($site_menu_id, $parent_item_id, $template = '')
    {
        if (!$template) {
            $template_file = 'site_menu_' . $site_menu_id . '_sub.tpl.php';

            if (!\Skif\PhpTemplate::existsTemplateBySkifModuleRelativeToRootSitePath('SiteMenu', $template_file)) {
                $template_file = 'site_sub_menu_default.tpl.php';
            }

            return \Skif\PhpTemplate::renderTemplateBySkifModule(
                'SiteMenu',
                $template_file,
                array('parent_item_id' => $parent_item_id)
            );
        }

        return \Skif\PhpTemplate::renderTemplate($template, array('parent_item_id' => $parent_item_id));
    }
}