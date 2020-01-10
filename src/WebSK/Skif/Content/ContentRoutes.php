<?php

namespace WebSK\Skif\Content;

use Slim\App;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentDeleteImageAction;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentListAutocompleteAction;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentListHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentUploadImageAction;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminRubricEditHandler;
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
use WebSK\Utils\HTTP;

/**
 * Class ContentRoutes
 * @package WebSK\Skif\Content
 */
class ContentRoutes
{
    const ROUTE_NAME_ADMIN_CONTENT_TYPE_LIST = 'admin:content_type:list';
    const ROUTE_NAME_ADMIN_CONTENT_TYPE_EDIT = 'admin:content_type:edit';

    const ROUTE_NAME_ADMIN_RUBRIC_LIST = 'admin:rubric:list';
    const ROUTE_NAME_ADMIN_RUBRIC_EDIT = 'admin:rubric:edit';

    const ROUTE_NAME_ADMIN_TEMPLATE_LIST = 'admin:template:list';
    const ROUTE_NAME_ADMIN_TEMPLATE_LIST_AJAX = 'admin:template:list:ajax';
    const ROUTE_NAME_ADMIN_TEMPLATE_EDIT = 'admin:template:edit';

    const ROUTE_NAME_ADMIN_CONTENT_LIST = 'admin:content:list';
    const ROUTE_NAME_ADMIN_CONTENT_LIST_AJAX = 'admin:content:list:ajax';
    const ROUTE_NAME_ADMIN_CONTENT_LIST_AUTOCOMPLETE = 'admin:content:list:autocomplete';

    const ROUTE_NAME_ADMIN_CONTENT_DELETE_IMAGE = 'admin:content:delete_image';
    const ROUTE_NAME_ADMIN_CONTENT_UPLOAD_IMAGE = 'admin:content:upload_image';

    const ROUTE_NAME_ADMIN_CONTENT_PHOTO_LIST = 'admin:content_photo:list';
    const ROUTE_NAME_ADMIN_CONTENT_PHOTO_CREATE = 'admin:content_photo:create';
    const ROUTE_NAME_CONTENT_PHOTO_DELETE = 'content_photo:delete';
    const ROUTE_NAME_CONTENT_PHOTO_SET_DEFAULT = 'content_photo:set_default';

    public static function route()
    {
        if (SimpleRouter::matchGroup('@/admin@')) {
            SimpleRouter::staticRoute('@^/admin/content/(\w+)/new$@i', ContentController::class, 'newAdminAction',
                0);
            SimpleRouter::staticRoute('@^/admin/content/(\w+)/save/(\w+)$@i', ContentController::class, 'saveAdminAction',
                0);
            SimpleRouter::staticRoute('@^/admin/content/(\w+)/delete/(\w+)$@i', ContentController::class, 'deleteAction',
                0);
            SimpleRouter::staticRoute('@^/admin/content/(\w+)$@i', ContentController::class, 'listAdminAction', 0);
        }
    }

    /**
     * @param App $app
     */
    public static function register(App $app)
    {
    }

    /**
     * @param App $app
     */
    public static function registerSimpleRoute(App $app)
    {
        SimpleRouter::route('@^@', [new ContentViewHandler($app->getContainer()), 'viewAction']);
        SimpleRouter::route('@^@', [new ContentInRubricListHandler($app->getContainer()), 'listAction']);
        SimpleRouter::route('@^/(.+)$@i', [new ContentListHandler($app->getContainer()), 'listAction']);
    }

    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {
        $app->group('/content', function (App $app) {
            $app->group('/{content_type:\w+}', function (App $app) {
                /*
                $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminContentListHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_CONTENT_LIST);
                */

                $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/ajax', AdminContentListAjaxHandler::class)
                    ->setName(self::ROUTE_NAME_ADMIN_CONTENT_LIST_AJAX);

                $app->group('/{content_id:\d+}', function (App $app) {
                    $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminContentEditHandler::class)
                        ->setName(AdminContentEditHandler::class);

                    $app->post('/content_photo/create', ContentPhotoCreateHandler::class)
                        ->setName(self::ROUTE_NAME_ADMIN_CONTENT_PHOTO_CREATE);

                    $app->get('/content_photo/list', AdminContentPhotoListHandler::class)
                        ->setName(self::ROUTE_NAME_ADMIN_CONTENT_PHOTO_LIST);

                    $app->post('/delete_image', AdminContentDeleteImageAction::class)
                        ->setName(self::ROUTE_NAME_ADMIN_CONTENT_DELETE_IMAGE);

                    $app->post('/upload_image', AdminContentUploadImageAction::class)
                        ->setName(self::ROUTE_NAME_ADMIN_CONTENT_UPLOAD_IMAGE);
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

        $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/content_autocomplete', AdminContentListAutocompleteAction::class)
            ->setName(self::ROUTE_NAME_ADMIN_CONTENT_LIST_AUTOCOMPLETE);

        $app->group('/content_photo/{content_photo_id:\d+}', function (App $app) {
            $app->post('/delete', ContentPhotoDeleteHandler::class)
                ->setName(self::ROUTE_NAME_CONTENT_PHOTO_DELETE);
            $app->post('/set_default', ContentPhotoSetDefaultHandler::class)
                ->setName(self::ROUTE_NAME_CONTENT_PHOTO_SET_DEFAULT);
        });
    }
}
