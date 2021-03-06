<?php

namespace WebSK\Skif\Poll;

use Slim\App;
use WebSK\Skif\Poll\RequestHandlers\Admin\AdminPollEditHandler;
use WebSK\Skif\Poll\RequestHandlers\Admin\AdminPollListAjaxHandler;
use WebSK\Skif\Poll\RequestHandlers\Admin\AdminPollListHandler;
use WebSK\Skif\Poll\RequestHandlers\Admin\AdminPollQuestionEditHandler;
use WebSK\Skif\Poll\RequestHandlers\PollViewHandler;
use WebSK\Skif\Poll\RequestHandlers\PollVoteHandler;
use WebSK\Utils\HTTP;

/**
 * Class PollRoutes
 * @package WebSK\Skif\Poll
 */
class PollRoutes
{
    const ROUTE_NAME_ADMIN_POLL_LIST = 'admin:poll:list';
    const ROUTE_NAME_ADMIN_POLL_LIST_AJAX = 'admin:poll:list_ajax';
    const ROUTE_NAME_ADMIN_POLL_EDIT = 'admin:poll:edit';
    const ROUTE_NAME_ADMIN_POLL_QUESTION_EDIT = 'admin:poll_question:edit';

    const ROUTE_NAME_POLL_VIEW = 'admin:poll:view';
    const ROUTE_NAME_POLL_VOTE = 'admin:poll:vote';

    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {
        $app->group('/poll', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminPollListHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_POLL_LIST);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/ajax', AdminPollListAjaxHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_POLL_LIST_AJAX);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{poll_id:\d+}', AdminPollEditHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_POLL_EDIT);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/poll_question/{poll_question_id:\d+}', AdminPollQuestionEditHandler::class)
                ->setName(self::ROUTE_NAME_ADMIN_POLL_QUESTION_EDIT);
        });
    }

    /**
     * @param App $app
     */
    public static function register(App $app)
    {
        $app->group('/poll', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{poll_id:\d+}', PollViewHandler::class)
                ->setName(self::ROUTE_NAME_POLL_VIEW);

            $app->post('/{poll_id:\d+}/vote', PollVoteHandler::class)
                ->setName(self::ROUTE_NAME_POLL_VOTE);
        });
    }
}
