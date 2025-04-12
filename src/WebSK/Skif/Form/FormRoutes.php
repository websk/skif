<?php

namespace WebSK\Skif\Form;

use Fig\Http\Message\RequestMethodInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Form\Middleware\FormViewMiddleware;
use WebSK\Skif\Form\RequestHandlers\Admin\AdminFormEditHandler;
use WebSK\Skif\Form\RequestHandlers\Admin\AdminFormFieldEditHandler;
use WebSK\Skif\Form\RequestHandlers\Admin\AdminFormListAjaxHandler;
use WebSK\Skif\Form\RequestHandlers\Admin\AdminFormListHandler;
use WebSK\Skif\Form\RequestHandlers\FormSendHandler;
use WebSK\Skif\Form\RequestHandlers\FormViewHandler;

/**
 * Class FormRoutes
 * @package WebSK\Skif\Form
 */
class FormRoutes
{
    /**
     * @param App $app
     */
    public static function register(App $app): void
    {
        /*
        $app->get('/{form_url}', FormViewHandler::class)
            ->setName(FormViewHandler::class)->add(new FormViewMiddleware($app->getContainer()->get(FormService::class)));
        */

        $app->group('/form', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->post('/{form_id:\d+}/send', FormSendHandler::class)
                ->setName(FormSendHandler::class);
        });
    }

    /**
     * @param App $app
     */
    public static function registerSimpleRoute(App $app): void
    {
        SimpleRouter::route('@^@', [new FormViewHandler($app->getContainer()), 'viewAction']);
    }

    /**
     * @param RouteCollectorProxyInterface $route_collector_proxy
     */
    public static function registerAdmin(RouteCollectorProxyInterface $route_collector_proxy): void
    {
        $route_collector_proxy->group('/form', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '', AdminFormListHandler::class)
                ->setName(AdminFormListHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/ajax', AdminFormListAjaxHandler::class)
                ->setName(AdminFormListAjaxHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/{form_id:\d+}', AdminFormEditHandler::class)
                ->setName(AdminFormEditHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/field/{form_field_id:\d+}', AdminFormFieldEditHandler::class)
                ->setName(AdminFormFieldEditHandler::class);
        });
    }
}
