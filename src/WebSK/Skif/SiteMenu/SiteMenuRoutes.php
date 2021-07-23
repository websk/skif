<?php

namespace WebSK\Skif\SiteMenu;

use Slim\App;
use WebSK\Skif\SiteMenu\RequestHandlers\AdminSiteMenuEditHandler;
use WebSK\Skif\SiteMenu\RequestHandlers\AdminSiteMenuItemEditHandler;
use WebSK\Skif\SiteMenu\RequestHandlers\AdminSiteMenuItemListAjaxHandler;
use WebSK\Skif\SiteMenu\RequestHandlers\AdminSiteMenuListAjaxHandler;
use WebSK\Skif\SiteMenu\RequestHandlers\AdminSiteMenuListHandler;
use WebSK\Utils\HTTP;

class SiteMenuRoutes
{
    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {
        $app->group('/site_menu', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminSiteMenuListHandler::class)
                ->setName(AdminSiteMenuListHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/ajax', AdminSiteMenuListAjaxHandler::class)
                ->setName(AdminSiteMenuListAjaxHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{site_menu_id:\d+}', AdminSiteMenuEditHandler::class)
                ->setName(AdminSiteMenuEditHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/site_menu_item/ajax', AdminSiteMenuItemListAjaxHandler::class)
                ->setName(AdminSiteMenuItemListAjaxHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/site_menu_item/{site_menu_item_id:\d+}', AdminSiteMenuItemEditHandler::class)
                ->setName(AdminSiteMenuItemEditHandler::class);
        });
    }

    /**
     * @param App $app
     */
    public static function register(App $app)
    {

    }
}
