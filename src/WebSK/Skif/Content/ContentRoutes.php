<?php

namespace WebSK\Skif\Content;

use Slim\App;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminRubricEditHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminRubricListHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentEditHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentListAjaxHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentPhotoCreateHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentPhotoDeleteHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentPhotoListHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentTypeEditHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentTypeListHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\SetDefaultContentPhotoHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminTemplateEditHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminTemplateListAjaxHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminTemplateListHandler;
use WebSK\Skif\Content\RequestHandlers\ContentViewHandler;
use WebSK\Utils\HTTP;
use WebSK\Utils\Url;

/**
 * Class ContentRoutes
 * @package WebSK\Skif\Content
 */
class ContentRoutes
{
    const ROUTE_NAME_ADMIN_CONTENT_TYPE_LIST = 'content_type:list';
    const ROUTE_NAME_ADMIN_CONTENT_TYPE_EDIT = 'content_type:edit';

    const ROUTE_NAME_ADMIN_RUBRIC_LIST = 'rubric:list';
    const ROUTE_NAME_ADMIN_RUBRIC_EDIT = 'rubric:edit';

    const ROUTE_NAME_ADMIN_TEMPLATE_LIST = 'template:list';
    const ROUTE_NAME_ADMIN_TEMPLATE_LIST_AJAX = 'template:list:ajax';
    const ROUTE_NAME_ADMIN_TEMPLATE_EDIT = 'template:edit';

    public static function route()
    {
        if (SimpleRouter::matchGroup('@/admin@')) {
            SimpleRouter::staticRoute('@^/admin/content/autocomplete$@i', ContentController::class,
                'autoCompleteContentAction', 0);
            SimpleRouter::staticRoute('@^/admin/content/(\w+)/new$@i', ContentController::class, 'newAdminAction',
                0);
            SimpleRouter::staticRoute('@^/admin/content/(\w+)/save/(\w+)$@i', ContentController::class, 'saveAdminAction',
                0);
            SimpleRouter::staticRoute('@^/admin/content/(\w+)/delete/(\w+)$@i', ContentController::class, 'deleteAction',
                0);
            SimpleRouter::staticRoute('@^/admin/content/(\w+)/delete_image/(\w+)$@i', ContentController::class,
                'deleteImageAction', 0);
            SimpleRouter::staticRoute('@^/admin/content/(\w+)$@i', ContentController::class, 'listAdminAction', 0);
        }

        SimpleRouter::route(
            '@^@',
            [new ContentController(), 'viewAction'],
            0
        );

        SimpleRouter::staticRoute('@^@', RubricController::class, 'listAction');
        SimpleRouter::staticRoute('@^/(.+)$@i', ContentController::class, 'listAction');
    }

    /**
     * @param App $app
     */
    public static function register(App $app)
    {
        $app->get('/[{content_url}]', ContentViewHandler::class)
            ->setName(ContentViewHandler::class)
            ->add(function ($request, $response, $next) use ($app) {
                $content_url = Url::getUriNoQueryString();

                $content_service = ContentServiceProvider::getContentService($app->getContainer());
                $content_id = $content_service->getIdByAlias($content_url);

                if (!$content_id) {
                    return $response;
                }

                $response = $next($request, $response);
                return $response;
            });

        $app->get('/news/[{content_url}]', ContentViewHandler::class)
            ->setName(ContentViewHandler::class);

        /*
        $app->get('/test', function ($request, $response) {
          return $response->getBody()->write(time());
        });
        */
    }

    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {
        $app->group('/content', function (App $app) {
            $app->group('/{content_type:\w+}', function (App $app) {
                $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/ajax', AdminContentListAjaxHandler::class)
                    ->setName(AdminContentListAjaxHandler::class);

                $app->group('/{content_id:\d+}', function (App $app) {
                    $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminContentEditHandler::class)
                        ->setName(AdminContentEditHandler::class);

                    $app->post('/content_photo/create', AdminContentPhotoCreateHandler::class)
                        ->setName(AdminContentPhotoCreateHandler::class);

                    $app->get('/content_photo/list', AdminContentPhotoListHandler::class)
                        ->setName(AdminContentPhotoListHandler::class);
                });

                $app->group('/rubric', function (App $app) {
                    $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminRubricListHandler::class)
                        ->setName(self::ROUTE_NAME_ADMIN_RUBRIC_LIST);

                    $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{rubric_id:\d+}', AdminRubricEditHandler::class)
                        ->setName(self::ROUTE_NAME_ADMIN_RUBRIC_EDIT);
                });
            });
        });

        $app->group('/content_type', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminContentTypeListHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_CONTENT_TYPE_LIST);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{content_type_id:\d+}', AdminContentTypeEditHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_CONTENT_TYPE_EDIT);
        });

        $app->group('/template', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminTemplateListHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_TEMPLATE_LIST);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/ajax', AdminTemplateListAjaxHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_TEMPLATE_LIST_AJAX);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{template_id:\d+}', AdminTemplateEditHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_TEMPLATE_EDIT);
        });

        $app->group('/content_photo/{content_photo_id:\d+}', function (App $app) {
            $app->post('/delete', AdminContentPhotoDeleteHandler::class)
                ->setName(AdminContentPhotoDeleteHandler::class);
            $app->post('/set_default', SetDefaultContentPhotoHandler::class)
                ->setName(SetDefaultContentPhotoHandler::class);
        });
    }
}
