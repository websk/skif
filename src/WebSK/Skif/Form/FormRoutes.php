<?php

namespace WebSK\Skif\Form;

use WebSK\SimpleRouter\SimpleRouter;

class FormRoutes
{
    public static function route()
    {
        SimpleRouter::routeBasedCrud('/admin/form', FormController::class);
        SimpleRouter::routeBasedCrud('/admin/form_field', FormController::class);

        SimpleRouter::staticRoute('@^@', FormController::class, 'viewAction');
        SimpleRouter::staticRoute('@^/form/(\d+)/send$@', FormController::class, 'sendAction');
    }
}
