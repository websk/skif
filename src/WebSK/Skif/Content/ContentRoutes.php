<?php

namespace WebSK\Skif\Content;

use Slim\App;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentDeleteImageAction;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentListAutocompleteAction;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentListHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentSaveHandler;
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
                $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminContentListHandler::class)
                    ->setName(AdminContentListHandler::class);

                $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/ajax', AdminContentListAjaxHandler::class)
                    ->setName(AdminContentListAjaxHandler::class);

                $app->group('/{content_id:\d+}', function (App $app) {
                    $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminContentEditHandler::class)
                        ->setName(AdminContentEditHandler::class);

                    $app->post('/save', AdminContentSaveHandler::class)
                        ->setName(AdminContentSaveHandler::class);

                    $app->post('/content_photo/create', ContentPhotoCreateHandler::class)
                        ->setName(ContentPhotoCreateHandler::class);

                    $app->get('/content_photo/list', AdminContentPhotoListHandler::class)
                        ->setName(AdminContentPhotoListHandler::class);

                    $app->post('/delete_image', AdminContentDeleteImageAction::class)
                        ->setName(AdminContentDeleteImageAction::class);

                    $app->post('/upload_image', AdminContentUploadImageAction::class)
                        ->setName(AdminContentUploadImageAction::class);
                });

                $app->group('/rubric', function (App $app) {
                    $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminRubricListHandler::class)
                        ->setName(AdminRubricListHandler::class);

                    $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{rubric_id:\d+}', AdminRubricEditHandler::class)
                        ->setName(AdminRubricEditHandler::class);
                });
            });
        });

        $app->group('/content_type', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminContentTypeListHandler::class)
                ->setName(AdminContentTypeListHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{content_type_id:\d+}', AdminContentTypeEditHandler::class)
                ->setName(AdminContentTypeEditHandler::class);
        });

        $app->group('/template', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminTemplateListHandler::class)
                ->setName(AdminTemplateListHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/ajax', AdminTemplateListAjaxHandler::class)
                ->setName(AdminTemplateListAjaxHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{template_id:\d+}', AdminTemplateEditHandler::class)
                ->setName(AdminTemplateEditHandler::class);
        });

        $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/content_autocomplete', AdminContentListAutocompleteAction::class)
            ->setName(AdminContentListAutocompleteAction::class);

        $app->group('/content_photo/{content_photo_id:\d+}', function (App $app) {
            $app->post('/delete', ContentPhotoDeleteHandler::class)
                ->setName(ContentPhotoDeleteHandler::class);
            $app->post('/set_default', ContentPhotoSetDefaultHandler::class)
                ->setName(ContentPhotoSetDefaultHandler::class);
        });
    }
}
