<?php

namespace WebSK\Skif\Comment;

use Fig\Http\Message\RequestMethodInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;
use WebSK\Skif\Comment\RequestHandlers\Admin\AdminCommentEditHandler;
use WebSK\Skif\Comment\RequestHandlers\Admin\AdminCommentListAjaxHandler;
use WebSK\Skif\Comment\RequestHandlers\Admin\AdminCommentListHandler;
use WebSK\Skif\Comment\RequestHandlers\CommentCreateHandler;
use WebSK\Skif\Comment\RequestHandlers\CommentListHandler;

/**
 * Class CommentRoutes
 * @package WebSK\Skif\Comment
 */
class CommentRoutes
{
    const string NAMESPACE_DIR = 'WebSK' . DIRECTORY_SEPARATOR . 'Skif' . DIRECTORY_SEPARATOR . 'Comment';

    /**
     * @param App $app
     */
    public static function register(App $app): void
    {
        $app->group('/comments', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->get('/list', CommentListHandler::class)
                ->setName(CommentListHandler::class);

            $route_collector_proxy->post('/create', CommentCreateHandler::class)
                ->setName(CommentCreateHandler::class);
        });
    }

    /**
     * @param RouteCollectorProxyInterface $route_collector_proxy
     */
    public static function registerAdmin(RouteCollectorProxyInterface $route_collector_proxy): void
    {
        $route_collector_proxy->group('/comments', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '', AdminCommentListHandler::class)
                ->setName(AdminCommentListHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/{comment_id:\d+}', AdminCommentEditHandler::class)
                ->setName(AdminCommentEditHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/ajax', AdminCommentListAjaxHandler::class)
                ->setName(AdminCommentListAjaxHandler::class);
        });
    }
}
