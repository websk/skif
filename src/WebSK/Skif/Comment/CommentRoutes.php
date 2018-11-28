<?php

namespace WebSK\Skif\Comment;

use Skif\UrlManager;

class CommentRoutes
{
    public static function route()
    {
        UrlManager::routeBasedCrud('/admin/comments', CommentController::class);

        UrlManager::route('@^/comments/list$@', CommentController::class, 'listWebAction');
        UrlManager::route('@^/comments/add$@', CommentController::class, 'saveWebAction');
        UrlManager::route('@^/comments/delete/(\d+)$@', CommentController::class, 'deleteWebAction');
    }
}
