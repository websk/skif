<?php

namespace WebSK\Skif\CRUD;

use WebSK\SimpleRouter\SimpleRouter;

class CRUDRoutes
{
    public static function route()
    {
        SimpleRouter::routeBasedCrud('/crud/[\d\w\%]+', CRUDController::class);
    }
}
