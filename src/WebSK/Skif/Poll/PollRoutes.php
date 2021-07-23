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
    /**
     * @param App $app
     */
    public static function registerAdmin(App $app)
    {
        $app->group('/poll', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '', AdminPollListHandler::class)
                ->setName(AdminPollListHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/ajax', AdminPollListAjaxHandler::class)
                ->setName(AdminPollListAjaxHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{poll_id:\d+}', AdminPollEditHandler::class)
                ->setName(AdminPollEditHandler::class);

            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/poll_question/{poll_question_id:\d+}', AdminPollQuestionEditHandler::class)
                ->setName(AdminPollQuestionEditHandler::class);
        });
    }

    /**
     * @param App $app
     */
    public static function register(App $app)
    {
        $app->group('/poll', function (App $app) {
            $app->map([HTTP::METHOD_GET, HTTP::METHOD_POST], '/{poll_id:\d+}', PollViewHandler::class)
                ->setName(PollViewHandler::class);

            $app->post('/{poll_id:\d+}/vote', PollVoteHandler::class)
                ->setName(PollVoteHandler::class);
        });
    }
}
