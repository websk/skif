<?php

namespace WebSK\Skif\SiteMenu;

use Fig\Http\Message\RequestMethodInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;
use WebSK\Skif\SiteMenu\RequestHandlers\AdminSiteMenuEditHandler;
use WebSK\Skif\SiteMenu\RequestHandlers\AdminSiteMenuItemEditHandler;
use WebSK\Skif\SiteMenu\RequestHandlers\AdminSiteMenuItemListAjaxHandler;
use WebSK\Skif\SiteMenu\RequestHandlers\AdminSiteMenuListAjaxHandler;
use WebSK\Skif\SiteMenu\RequestHandlers\AdminSiteMenuListHandler;

class SiteMenuRoutes
{
    /**
     * @param RouteCollectorProxyInterface $route_collector_proxy
     */
    public static function registerAdmin(RouteCollectorProxyInterface $route_collector_proxy): void
    {
        $route_collector_proxy->group('/site_menu', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '', AdminSiteMenuListHandler::class)
                ->setName(AdminSiteMenuListHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/ajax', AdminSiteMenuListAjaxHandler::class)
                ->setName(AdminSiteMenuListAjaxHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/{site_menu_id:\d+}', AdminSiteMenuEditHandler::class)
                ->setName(AdminSiteMenuEditHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/site_menu_item/ajax', AdminSiteMenuItemListAjaxHandler::class)
                ->setName(AdminSiteMenuItemListAjaxHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/site_menu_item/{site_menu_item_id:\d+}', AdminSiteMenuItemEditHandler::class)
                ->setName(AdminSiteMenuItemEditHandler::class);
        });
    }

    /**
     * @param App $app
     */
    public static function register(App $app): void
    {

    }
}
