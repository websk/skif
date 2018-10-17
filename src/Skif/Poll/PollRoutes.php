<?php

namespace Skif\Poll;

use Skif\UrlManager;

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
