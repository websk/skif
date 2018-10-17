<?php

namespace Skif\Logger;

use Skif\Router;
use Skif\UrlManager;

class LoggerRoutes
{
    public static function route()
    {
        if (!Router::matchGroup('@/admin@')) {
            return;
        }

        UrlManager::route('@^/admin/logger/list$@i', ControllerLogger::class, 'listAction', 0);
        UrlManager::route('@^/admin/logger/object_log/@i', ControllerLogger::class, 'object_logAction', 0);
        UrlManager::route('@^/admin/logger/record/@', ControllerLogger::class, 'recordAction', 0);
    }
}
