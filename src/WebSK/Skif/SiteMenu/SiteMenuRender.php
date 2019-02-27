<?php

namespace WebSK\Skif\SiteMenu;

use WebSK\Views\PhpRender;
use WebSK\Views\ViewsPath;

/**
 * Class SiteMenuRender
 * @package WebSK\Skif\SiteMenu
 */
class SiteMenuRender
{
    public static function renderSiteMenu($site_menu_id, $template = '')
    {
        if (!$template) {
            $template_file = 'site_menu_' . $site_menu_id . '.tpl.php';

            if (!ViewsPath::existsTemplateByModuleRelativeToRootSitePath('WebSK/Skif/SiteMenu', $template_file)) {
                $template_file = 'site_menu_default.tpl.php';
            }

            return PhpRender::renderTemplateByModule(
                'WebSK/Skif/SiteMenu',
                $template_file,
                array('site_menu_id' => $site_menu_id)
            );
        }

        return PhpRender::renderTemplate($template, array('site_menu_id' => $site_menu_id));
    }

    public static function renderSiteSubMenu($site_menu_id, $parent_item_id, $template = '')
    {
        if (!$template) {
            $template_file = 'site_menu_' . $site_menu_id . '_sub.tpl.php';

            if (!ViewsPath::existsTemplateByModuleRelativeToRootSitePath('WebSK/Skif/SiteMenu', $template_file)) {
                $template_file = 'site_sub_menu_default.tpl.php';
            }

            return PhpRender::renderTemplateByModule(
                'WebSK/Skif/SiteMenu',
                $template_file,
                array('parent_item_id' => $parent_item_id)
            );
        }

        return PhpRender::renderTemplate($template, array('parent_item_id' => $parent_item_id));
    }
}
