<?php

namespace WebSK\Skif\Comment;

use WebSK\SimpleRouter\SimpleRouter;

class CommentRoutes
{
    public static function route()
    {
        SimpleRouter::routeBasedCrud('/admin/comments', CommentController::class);

        SimpleRouter::staticRoute('@^/comments/list$@', CommentController::class, 'listWebAction');
        SimpleRouter::staticRoute('@^/comments/add$@', CommentController::class, 'saveWebAction');
        SimpleRouter::staticRoute('@^/comments/delete/(\d+)$@', CommentController::class, 'deleteWebAction');
    }
}
