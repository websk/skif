<?php

namespace WebSK\Skif\Comment;

use Slim\App;
use WebSK\Skif\Comment\RequestHandlers\Admin\AdminCommentEditHandler;
use WebSK\Skif\Comment\RequestHandlers\Admin\AdminCommentListHandler;
use WebSK\Skif\Comment\RequestHandlers\CommentCreateHandler;
use WebSK\Skif\Comment\RequestHandlers\CommentListHandler;
use WebSK\Utils\HTTP;

/**
 * Class CommentRoutes
 * @package WebSK\Skif\Comment
 */
class CommentRoutes
{
    const ROUTE_NAME_ADMIN_COMMENTS_LIST = 'admin:comments:list';
    const ROUTE_NAME_ADMIN_COMMENTS_EDIT = 'admin:comments:edit';
    const ROUTE_NAME_COMMENTS_LIST = 'comments:list';
    const ROUTE_NAME_COMMENTS_CREATE = 'comments:create';

    /**
     * @param App $app
     */
    public static function register(App $app)
    {
        $app->group('/comments', function (App $app) {
            $app->get('/list', CommentListHandler::class)
                ->setName(self::ROUTE_NAME_COMMENTS_LIST);

            $app->post('/create', CommentCreateHandler::class)
                ->setName(self::ROUTE_NAME_COMMENTS_CREATE);
        });
    }

    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {
        $app->group('/comments', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminCommentListHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_COMMENTS_LIST);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{comment_id:\d+}', AdminCommentEditHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_COMMENTS_EDIT);
        });
    }
}
