<?php

namespace WebSK\Skif\CRUD;

use WebSK\Skif\UrlManager;

class CRUDRoutes
{
    public static function route()
    {
        UrlManager::routeBasedCrud('/crud/[\d\w\%]+', CRUDController::class);
    }
}
