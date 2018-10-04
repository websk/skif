<?php

namespace Skif\Form;

use Skif\UrlManager;

class FormRoutes
{
    public static function route()
    {
        $current_url_no_query = UrlManager::getUriNoQueryString();

        $route_based_crud_arr = array(
            '/admin/form' => FormController::class,
            '/admin/form_field' => FormFieldController::class,
        );

        foreach ($route_based_crud_arr as $base_url => $controller) {
            if (!preg_match('@^' . $base_url . '?(.+)@i', $current_url_no_query, $matches_arr)) {
                continue;
            }

            UrlManager::route('@^' . $base_url . '/add$@', $controller, 'addAction', 0);
            UrlManager::route('@^' . $base_url . '/create$@', $controller, 'createAction', 0);
            UrlManager::route('@^' . $base_url . '/edit/(.+)$@', $controller, 'editAction', 0);
            UrlManager::route('@^' . $base_url . '/save/(.+)$@i', $controller, 'saveAction', 0);
            UrlManager::route('@^' . $base_url . '/delete/(\d+)$@i', $controller, 'deleteAction', 0);
            UrlManager::route('@^' . $base_url . '$@i', $controller, 'listAction', 0);
        }

        UrlManager::route('@^@', FormController::class, 'viewAction');
        UrlManager::route('@^/form/(\d+)/send$@', FormController::class, 'sendAction');
    }
}
