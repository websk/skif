<?php

namespace WebSK\Skif\Content;

use Fig\Http\Message\RequestMethodInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentDeleteImageAction;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentListAutocompleteAction;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentListHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentSaveHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentUploadImageAction;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminRubricEditHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminRubricListAjaxHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminRubricListHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentEditHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentListAjaxHandler;
use WebSK\Skif\Content\RequestHandlers\ContentPhotoCreateHandler;
use WebSK\Skif\Content\RequestHandlers\ContentPhotoDeleteHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentPhotoListHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentTypeEditHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentTypeListHandler;
use WebSK\Skif\Content\RequestHandlers\ContentPhotoSetDefaultHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminTemplateEditHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminTemplateListAjaxHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminTemplateListHandler;
use WebSK\Skif\Content\RequestHandlers\ContentListHandler;
use WebSK\Skif\Content\RequestHandlers\ContentViewHandler;
use WebSK\Skif\Content\RequestHandlers\ContentInRubricListHandler;

/**
 * Class ContentRoutes
 * @package WebSK\Skif\Content
 */
class ContentRoutes
{
    /**
     * @param App $app
     */
    public static function register(App $app)
    {
    }

    /**
     * @param App $app
     */
    public static function registerSimpleRoute(App $app): void
    {
        SimpleRouter::route('@^@', [new ContentViewHandler($app->getContainer()), 'viewAction']);
        SimpleRouter::route('@^@', [new ContentInRubricListHandler($app->getContainer()), 'listAction']);
        SimpleRouter::route('@^/(.+)$@i', [new ContentListHandler($app->getContainer()), 'listAction']);
    }

    /**
     * @param RouteCollectorProxyInterface $route_collector_proxy
     */
    public static function registerAdmin(RouteCollectorProxyInterface $route_collector_proxy): void
    {
        $route_collector_proxy->group('/content', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->group('/{content_type:\w+}', function (RouteCollectorProxyInterface $route_collector_proxy) {
                $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '', AdminContentListHandler::class)
                    ->setName(AdminContentListHandler::class);

                $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/ajax', AdminContentListAjaxHandler::class)
                    ->setName(AdminContentListAjaxHandler::class);

                $route_collector_proxy->group('/{content_id:\d+}', function (RouteCollectorProxyInterface $route_collector_proxy) {
                    $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '', AdminContentEditHandler::class)
                        ->setName(AdminContentEditHandler::class);

                    $route_collector_proxy->post('/save', AdminContentSaveHandler::class)
                        ->setName(AdminContentSaveHandler::class);

                    $route_collector_proxy->post('/content_photo/create', ContentPhotoCreateHandler::class)
                        ->setName(ContentPhotoCreateHandler::class);

                    $route_collector_proxy->get('/content_photo/list', AdminContentPhotoListHandler::class)
                        ->setName(AdminContentPhotoListHandler::class);

                    $route_collector_proxy->post('/delete_image', AdminContentDeleteImageAction::class)
                        ->setName(AdminContentDeleteImageAction::class);

                    $route_collector_proxy->post('/upload_image', AdminContentUploadImageAction::class)
                        ->setName(AdminContentUploadImageAction::class);
                });

                $route_collector_proxy->group('/rubric', function (RouteCollectorProxyInterface $route_collector_proxy) {
                    $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '', AdminRubricListHandler::class)
                        ->setName(AdminRubricListHandler::class);

                    $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/ajax', AdminRubricListAjaxHandler::class)
                        ->setName(AdminRubricListAjaxHandler::class);

                    $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/{rubric_id:\d+}', AdminRubricEditHandler::class)
                        ->setName(AdminRubricEditHandler::class);
                });
            });
        });

        $route_collector_proxy->group('/content_type', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '', AdminContentTypeListHandler::class)
                ->setName(AdminContentTypeListHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/{content_type_id:\d+}', AdminContentTypeEditHandler::class)
                ->setName(AdminContentTypeEditHandler::class);
        });

        $route_collector_proxy->group('/template', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '', AdminTemplateListHandler::class)
                ->setName(AdminTemplateListHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/ajax', AdminTemplateListAjaxHandler::class)
                ->setName(AdminTemplateListAjaxHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/{template_id:\d+}', AdminTemplateEditHandler::class)
                ->setName(AdminTemplateEditHandler::class);
        });

        $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/content_autocomplete', AdminContentListAutocompleteAction::class)
            ->setName(AdminContentListAutocompleteAction::class);

        $route_collector_proxy->group('/content_photo/{content_photo_id:\d+}', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->post('/delete', ContentPhotoDeleteHandler::class)
                ->setName(ContentPhotoDeleteHandler::class);
            $route_collector_proxy->post('/set_default', ContentPhotoSetDefaultHandler::class)
                ->setName(ContentPhotoSetDefaultHandler::class);
        });
    }
}
