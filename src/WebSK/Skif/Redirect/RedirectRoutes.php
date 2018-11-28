<?php

namespace WebSK\Skif\Redirect;

use WebSK\Skif\UrlManager;

class RedirectRoutes
{
    public static function route()
    {
        UrlManager::route('@^@', RedirectController::class, 'redirectAction');

        UrlManager::routeBasedCrud('/admin/redirect', RedirectController::class);
    }
}
