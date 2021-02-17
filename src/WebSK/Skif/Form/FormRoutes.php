<?php

namespace WebSK\Skif\Form;

use Slim\App;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Form\RequestHandlers\Admin\AdminFormEditHandler;
use WebSK\Skif\Form\RequestHandlers\Admin\AdminFormFieldEditHandler;
use WebSK\Skif\Form\RequestHandlers\Admin\AdminFormListAjaxHandler;
use WebSK\Skif\Form\RequestHandlers\Admin\AdminFormListHandler;
use WebSK\Skif\Form\RequestHandlers\FormSendHandler;
use WebSK\Skif\Form\RequestHandlers\FormViewHandler;
use WebSK\Utils\HTTP;

/**
 * Class FormRoutes
 * @package WebSK\Skif\Form
 */
class FormRoutes
{
    /**
     * @param App $app
     */
    public static function register(App $app)
    {
        $app->group('/form', function (App $app) {
            $app->post('/{form_id:\d+}/send', FormSendHandler::class)
                ->setName(FormSendHandler::class);
        });
    }

    /**
     * @param App $app
     */
    public static function registerSimpleRoute(App $app)
    {
        SimpleRouter::route('@^@', [new FormViewHandler($app->getContainer()), 'viewAction']);
    }

    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {
        $app->group('/form', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminFormListHandler::class)
                ->setName(AdminFormListHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/ajax', AdminFormListAjaxHandler::class)
                ->setName(AdminFormListAjaxHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{form_id:\d+}', AdminFormEditHandler::class)
                ->setName(AdminFormEditHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/field/{form_field_id:\d+}', AdminFormFieldEditHandler::class)
                ->setName(AdminFormFieldEditHandler::class);
        });
    }
}
