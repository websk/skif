<?php

namespace WebSK\Skif\Poll;

use WebSK\Skif\UrlManager;

/**
 * Class PollRoutes
 * @package WebSK\Skif\Poll
 */
class PollRoutes
{
    public static function route()
    {
        UrlManager::routeBasedCrud('/admin/poll', PollController::class);
        UrlManager::routeBasedCrud('/admin/poll_question', PollQuestionController::class);

        UrlManager::route('@^/poll/(\d+)$@', PollController::class, 'viewAction');
        UrlManager::route('@^/poll/(\d+)/vote$@', PollController::class, 'voteAction');
    }
}
