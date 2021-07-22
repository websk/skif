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
    /**
     * @param int $site_menu_id
     * @param string $template
     * @return string
     */
    public static function renderSiteMenu(int $site_menu_id, string $template = ''): string
    {
        if (!$template) {
            $template_file = 'site_menu_' . $site_menu_id . '.tpl.php';

            if (!ViewsPath::existsTemplateByModuleRelativeToRootSitePath('WebSK/Skif/SiteMenu', $template_file)) {
                return PhpRender::renderTemplateInViewsDir(
                    'site_menu_default.tpl.php',
                    array('site_menu_id' => $site_menu_id)
                );
            }

            return PhpRender::renderTemplateForModuleNamespace(
                'WebSK/Skif/SiteMenu',
                $template_file,
                array('site_menu_id' => $site_menu_id)
            );
        }

        return PhpRender::renderTemplate($template, array('site_menu_id' => $site_menu_id));
    }

    /**
     * @param int $site_menu_id
     * @param int $parent_item_id
     * @param string $template
     * @return string
     */
    public static function renderSiteSubMenu(int $site_menu_id, int $parent_item_id, string $template = ''): string
    {
        if (!$template) {
            $template = 'site_menu_' . $site_menu_id . '_sub.tpl.php';
        }

        if (!ViewsPath::existsTemplateByModuleRelativeToRootSitePath('WebSK/Skif/SiteMenu', $template)) {
            return PhpRender::renderTemplateInViewsDir(
                'site_sub_menu_default.tpl.php',
                ['parent_item_id' => $parent_item_id]
            );
        }

        return PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/SiteMenu',
            $template,
            ['parent_item_id' => $parent_item_id]
        );
    }
}
