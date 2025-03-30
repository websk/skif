<?php

namespace WebSK\Skif\Redirect;

use Fig\Http\Message\RequestMethodInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Redirect\RequestHandlers\Admin\AdminRedirectEditHandler;
use WebSK\Skif\Redirect\RequestHandlers\Admin\AdminRedirectListHandler;
use WebSK\Skif\Redirect\RequestHandlers\RedirectHandler;

/**
 * Class RedirectRoutes
 * @package WebSK\Skif\Redirect
 */
class RedirectRoutes
{

    /**
     * @param App $app
     */
    public static function registerSimpleRoute(App $app): void
    {
        SimpleRouter::route('@^@', [new RedirectHandler($app->getContainer()), 'redirectAction']);
    }

    /**
     * @param RouteCollectorProxyInterface $route_collector_proxy
     */
    public static function registerAdmin(RouteCollectorProxyInterface $route_collector_proxy): void
    {
        $route_collector_proxy->group('/redirect', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '', AdminRedirectListHandler::class)
                ->setName(AdminRedirectListHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/{redirect_id:\d+}', AdminRedirectEditHandler::class)
                ->setName(AdminRedirectEditHandler::class);
        });
    }
}
