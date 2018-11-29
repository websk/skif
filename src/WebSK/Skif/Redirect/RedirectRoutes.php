<?php

namespace WebSK\Skif\Redirect;

use WebSK\SimpleRouter\SimpleRouter;

class RedirectRoutes
{
    public static function route()
    {
        SimpleRouter::staticRoute('@^@', RedirectController::class, 'redirectAction');

        SimpleRouter::routeBasedCrud('/admin/redirect', RedirectController::class);
    }
}
