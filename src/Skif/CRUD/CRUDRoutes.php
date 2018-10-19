<?php

namespace Skif\CRUD;

use Skif\UrlManager;

class CRUDRoutes
{
    public static function route()
    {
        UrlManager::routeBasedCrud('/crud/[\d\w\%]+', CRUDController::class);
    }
}
