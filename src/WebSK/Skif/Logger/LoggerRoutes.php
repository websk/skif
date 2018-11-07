<?php

namespace WebSK\Skif\Logger;

use Slim\App;
use VitrinaTV\Core\Auth\Middleware\CurrentUserHasAnyOfPermissions;
use WebSK\Utils\HTTP;
use WebSK\Skif\Logger\RequestHandlers\EntriesListHandler;
use WebSK\Skif\Logger\RequestHandlers\EntryEditHandler;
use WebSK\Skif\Logger\RequestHandlers\ObjectEntriesListHandler;

class LoggerRoutes
{
    const ROUTE_NAME_ADMIN_LOGGER_ENTRIES_LIST = 'admin:logger:entries';
    const ROUTE_NAME_ADMIN_LOGGER_ENTRY_EDIT = 'admin:logger:entry';
    const ROUTE_NAME_ADMIN_LOGGER_OBJECT_ENTRIES_LIST = 'admin:logger:object_entries';

    /**
     * @param App $app
     */
    public static function register(App $app)
    {
        $app->group('/logger', function (App $app) {
            $app->group('/entry', function (App $app) {
                $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', EntriesListHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_LOGGER_ENTRIES_LIST);

                $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{entry_id:\d+}', EntryEditHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_LOGGER_ENTRY_EDIT);
            });
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], 'objectentries/{object_full_id}', ObjectEntriesListHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_LOGGER_OBJECT_ENTRIES_LIST);
        })->add(new CurrentUserHasAnyOfPermissions(
            [LoggerPermissions::PERMISSION_LOGGER_ACCESS]
        ));
    }
}
