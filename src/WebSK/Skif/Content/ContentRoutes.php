<?php

namespace WebSK\Skif\Content;

use Skif\Router;
use Skif\UrlManager;

class ContentRoutes
{
    public static function route()
    {
        if (Router::matchGroup('@/admin@')) {
            UrlManager::route('@^/admin/content/(.+)/rubrics$@', RubricController::class, 'listAdminRubricsAction');
            UrlManager::route('@^/admin/content/(.+)/rubrics/edit/(.+)@', RubricController::class, 'editRubricAction');
            UrlManager::route('@^/admin/content/(.+)/rubrics/save/(.+)@', RubricController::class, 'saveRubricAction');
            UrlManager::route('@^/admin/content/(.+)/rubrics/delete/(.+)@', RubricController::class, 'deleteRubricAction');
            UrlManager::route('@^/admin/content/autocomplete$@i', ContentController::class, 'autoCompleteContentAction', 0);
            UrlManager::route('@^/admin/content/(.+)/edit/(.+)$@i', ContentController::class, 'editAdminAction', 0);
            UrlManager::route('@^/admin/content/(.+)/save/(.+)$@i', ContentController::class, 'saveAdminAction', 0);
            UrlManager::route('@^/admin/content/(.+)/delete/(.+)$@i', ContentController::class, 'deleteAction', 0);
            UrlManager::route('@^/admin/content/(.+)/delete_image/(.+)$@i', ContentController::class, 'deleteImageAction', 0);
            UrlManager::route('@^/admin/content/(.+)$@i', ContentController::class, 'listAdminAction', 0);
        }

        Router::route(
            '@^@',
            [new ContentController(), 'viewAction'],
            0
        );

        UrlManager::route('@^@', RubricController::class, 'listAction');
        UrlManager::route('@^/(.+)$@i', ContentController::class, 'listAction');
    }
}
