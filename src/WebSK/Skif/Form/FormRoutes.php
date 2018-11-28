<?php

namespace WebSK\Skif\Form;

use Skif\UrlManager;

class FormRoutes
{
    public static function route()
    {
        UrlManager::routeBasedCrud('/admin/form', FormController::class);
        UrlManager::routeBasedCrud('/admin/form_field', FormController::class);

        UrlManager::route('@^@', FormController::class, 'viewAction');
        UrlManager::route('@^/form/(\d+)/send$@', FormController::class, 'sendAction');
    }
}
