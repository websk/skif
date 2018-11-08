<?php

namespace WebSK\Skif\KeyValue;

use Slim\App;
use WebSK\Skif\Users\Middleware\CurrentUserHasAnyOfPermissions;
use WebSK\Utils\HTTP;
use WebSK\Skif\KeyValue\RequestHandlers\KeyValueEditHandler;
use WebSK\Skif\KeyValue\RequestHandlers\KeyValueListHandler;

/**
 * Class KeyValueRoutes
 * @package WebSK\Skif\KeyValue
 */
class KeyValueRoutes
{
    const ADMIN_ROOT_PATH = '/admin';

    /**
     * @param App $app
     */
    public static function registerAdmin(App $app): void
    {
        $app->group('/key_value', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', KeyValueListHandler::class)
                ->setName(KeyValueListHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{keyvalue_id:\d+}', KeyValueEditHandler::class)
                ->setName(KeyValueEditHandler::class);
        })->add(new CurrentUserHasAnyOfPermissions(
            [KeyValuePermissions::PERMISSION_KEYVALUE_MANAGE]
        ));
    }
}
