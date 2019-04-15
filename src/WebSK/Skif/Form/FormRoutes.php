<?php

namespace WebSK\Skif\Form;

use WebSK\SimpleRouter\SimpleRouter;

/**
 * Class FormRoutes
 * @package WebSK\Skif\Form
 */
class FormRoutes
{
    public static function route()
    {
        SimpleRouter::routeBasedCrud('/admin/form', FormController::class);
        SimpleRouter::routeBasedCrud('/admin/form_field', FormFieldController::class);

        SimpleRouter::staticRoute('@^@', FormController::class, 'viewAction');
        SimpleRouter::staticRoute('@^/form/(\d+)/send$@', FormController::class, 'sendAction');
    }
}
