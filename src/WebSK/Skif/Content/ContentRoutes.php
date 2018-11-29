<?php

namespace WebSK\Skif\Content;

use WebSK\SimpleRouter\SimpleRouter;

class ContentRoutes
{
    public static function route()
    {
        if (SimpleRouter::matchGroup('@/admin@')) {
            SimpleRouter::staticRoute('@^/admin/content/(.+)/rubrics$@', RubricController::class,
                'listAdminRubricsAction');
            SimpleRouter::staticRoute('@^/admin/content/(.+)/rubrics/edit/(.+)@', RubricController::class,
                'editRubricAction');
            SimpleRouter::staticRoute('@^/admin/content/(.+)/rubrics/save/(.+)@', RubricController::class,
                'saveRubricAction');
            SimpleRouter::staticRoute('@^/admin/content/(.+)/rubrics/delete/(.+)@', RubricController::class,
                'deleteRubricAction');
            SimpleRouter::staticRoute('@^/admin/content/autocomplete$@i', ContentController::class,
                'autoCompleteContentAction', 0);
            SimpleRouter::staticRoute('@^/admin/content/(.+)/edit/(.+)$@i', ContentController::class, 'editAdminAction',
                0);
            SimpleRouter::staticRoute('@^/admin/content/(.+)/save/(.+)$@i', ContentController::class, 'saveAdminAction',
                0);
            SimpleRouter::staticRoute('@^/admin/content/(.+)/delete/(.+)$@i', ContentController::class, 'deleteAction',
                0);
            SimpleRouter::staticRoute('@^/admin/content/(.+)/delete_image/(.+)$@i', ContentController::class,
                'deleteImageAction', 0);
            SimpleRouter::staticRoute('@^/admin/content/(.+)$@i', ContentController::class, 'listAdminAction', 0);
        }

        SimpleRouter::route(
            '@^@',
            [new ContentController(), 'viewAction'],
            0
        );

        SimpleRouter::staticRoute('@^@', RubricController::class, 'listAction');
        SimpleRouter::staticRoute('@^/(.+)$@i', ContentController::class, 'listAction');
    }
}
