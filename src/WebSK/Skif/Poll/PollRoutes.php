<?php

namespace WebSK\Skif\Poll;

use Fig\Http\Message\RequestMethodInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;
use WebSK\Skif\Poll\RequestHandlers\Admin\AdminPollEditHandler;
use WebSK\Skif\Poll\RequestHandlers\Admin\AdminPollListAjaxHandler;
use WebSK\Skif\Poll\RequestHandlers\Admin\AdminPollListHandler;
use WebSK\Skif\Poll\RequestHandlers\Admin\AdminPollQuestionEditHandler;
use WebSK\Skif\Poll\RequestHandlers\PollViewHandler;
use WebSK\Skif\Poll\RequestHandlers\PollVoteHandler;

/**
 * Class PollRoutes
 * @package WebSK\Skif\Poll
 */
class PollRoutes
{
    /**
     * @param RouteCollectorProxyInterface $route_collector_proxy
     */
    public static function registerAdmin(RouteCollectorProxyInterface $route_collector_proxy): void
    {
        $route_collector_proxy->group('/poll', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '', AdminPollListHandler::class)
                ->setName(AdminPollListHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/ajax', AdminPollListAjaxHandler::class)
                ->setName(AdminPollListAjaxHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/{poll_id:\d+}', AdminPollEditHandler::class)
                ->setName(AdminPollEditHandler::class);

            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/poll_question/{poll_question_id:\d+}', AdminPollQuestionEditHandler::class)
                ->setName(AdminPollQuestionEditHandler::class);
        });
    }

    /**
     * @param App $app
     */
    public static function register(App $app): void
    {
        $app->group('/poll', function (RouteCollectorProxyInterface $route_collector_proxy) {
            $route_collector_proxy->map([RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST], '/{poll_id:\d+}', PollViewHandler::class)
                ->setName(PollViewHandler::class);

            $route_collector_proxy->post('/{poll_id:\d+}/vote', PollVoteHandler::class)
                ->setName(PollVoteHandler::class);
        });
    }
}
