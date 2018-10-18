<?php

namespace Skif\KeyValue;

use Skif\UrlManager;

class KeyValueRoutes
{
    public static function route()
    {
        UrlManager::routeBasedCrud('/admin/key_value', KeyValueController::class);
    }
}
