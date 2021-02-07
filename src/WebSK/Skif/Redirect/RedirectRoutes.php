<?php

namespace WebSK\Skif\Redirect;

use Slim\App;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Redirect\RequestHandlers\Admin\AdminRedirectEditHandler;
use WebSK\Skif\Redirect\RequestHandlers\Admin\AdminRedirectListHandler;
use WebSK\Skif\Redirect\RequestHandlers\RedirectHandler;
use WebSK\Utils\HTTP;

/**
 * Class RedirectRoutes
 * @package WebSK\Skif\Redirect
 */
class RedirectRoutes
{
    const ROUTE_NAME_ADMIN_REDIRECT_LIST = 'admin:redirect:list';
    const ROUTE_NAME_ADMIN_REDIRECT_EDIT = 'admin:redirect:edit';


    /**
     * @param App $app
     */
    public static function registerSimpleRoute(App $app)
    {
        SimpleRouter::route('@^@', [new RedirectHandler($app->getContainer()), 'redirectAction']);
    }

    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {
        $app->group('/redirect', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminRedirectListHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_REDIRECT_LIST);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{redirect_id:\d+}', AdminRedirectEditHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_REDIRECT_EDIT);
        });
    }
}
