<?php

namespace WebSK\Skif\Form;

use Slim\App;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Form\RequestHandlers\Admin\AdminFormEditHandler;
use WebSK\Skif\Form\RequestHandlers\Admin\AdminFormFieldEditHandler;
use WebSK\Skif\Form\RequestHandlers\Admin\AdminFormListAjaxHandler;
use WebSK\Skif\Form\RequestHandlers\Admin\AdminFormListHandler;
use WebSK\Skif\Form\RequestHandlers\FormSendHandler;
use WebSK\Utils\HTTP;

/**
 * Class FormRoutes
 * @package WebSK\Skif\Form
 */
class FormRoutes
{
    const ROUTE_NAME_ADMIN_FORM_LIST = 'admin:form:list';
    const ROUTE_NAME_ADMIN_FORM_LIST_AJAX = 'admin:form:list_ajax';
    const ROUTE_NAME_ADMIN_FORM_EDIT = 'admin:form:edit';
    const ROUTE_NAME_ADMIN_FORM_FIELD_EDIT = 'admin:form:field:edit';

    const ROUTE_NAME_FORM_SEND = 'form:send';


    public static function route()
    {
        SimpleRouter::staticRoute('@^@', FormController::class, 'viewAction');
    }

    /**
     * @param App $app
     */
    public static function register(App $app)
    {
        $app->group('/form', function (App $app) {
            $app->post('/{form_id:\d+}/send', FormSendHandler::class)
                ->setName(self::ROUTE_NAME_FORM_SEND);
        });
    }

    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {
        $app->group('/form', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminFormListHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_FORM_LIST);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/ajax', AdminFormListAjaxHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_FORM_LIST_AJAX);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{form_id:\d+}', AdminFormEditHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_FORM_EDIT);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/field/{form_field_id:\d+}', AdminFormFieldEditHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_FORM_FIELD_EDIT);
        });
    }
}
