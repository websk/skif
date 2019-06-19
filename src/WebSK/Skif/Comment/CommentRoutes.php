<?php

namespace WebSK\Skif\Comment;

use Slim\App;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Comment\RequestHandlers\CommentCreateHandler;
use WebSK\Skif\Comment\RequestHandlers\CommentListHandler;

/**
 * Class CommentRoutes
 * @package WebSK\Skif\Comment
 */
class CommentRoutes
{
    const ROUTE_NAME_COMMENTS_LIST = 'comments:list';
    const ROUTE_NAME_COMMENTS_CREATE = 'comments:create';

    public static function route()
    {
        SimpleRouter::routeBasedCrud('/admin/comments', CommentController::class);
    }

    /**
     * @param App $app
     */
    public static function register(App $app)
    {
        $app->group('/comments', function (App $app) {
            $app->get('/list', CommentListHandler::class)
                ->setName(self::ROUTE_NAME_COMMENTS_LIST);

            $app->post('/comments/create', CommentCreateHandler::class)
                ->setName(self::ROUTE_NAME_COMMENTS_CREATE);
        });
    }
}
