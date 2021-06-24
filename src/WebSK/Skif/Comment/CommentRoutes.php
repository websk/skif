<?php

namespace WebSK\Skif\Comment;

use Slim\App;
use WebSK\Skif\Comment\RequestHandlers\Admin\AdminCommentEditHandler;
use WebSK\Skif\Comment\RequestHandlers\Admin\AdminCommentListAjaxHandler;
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
    const NAMESPACE_DIR = 'WebSK' . DIRECTORY_SEPARATOR . 'Skif' . DIRECTORY_SEPARATOR . 'Comment';

    /**
     * @param App $app
     */
    public static function register(App $app)
    {
        $app->group('/comments', function (App $app) {
            $app->get('/list', CommentListHandler::class)
                ->setName(CommentListHandler::class);

            $app->post('/create', CommentCreateHandler::class)
                ->setName(CommentCreateHandler::class);
        });
    }

    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {
        $app->group('/comments', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminCommentListHandler::class)
                ->setName(AdminCommentListHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{comment_id:\d+}', AdminCommentEditHandler::class)
                ->setName(AdminCommentEditHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/ajax', AdminCommentListAjaxHandler::class)
                ->setName(AdminCommentListAjaxHandler::class);
        });
    }
}
