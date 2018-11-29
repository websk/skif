<?php

namespace WebSK\Skif\Poll;

use WebSK\SimpleRouter\SimpleRouter;

/**
 * Class PollRoutes
 * @package WebSK\Skif\Poll
 */
class PollRoutes
{
    public static function route()
    {
        SimpleRouter::routeBasedCrud('/admin/poll', PollController::class);
        SimpleRouter::routeBasedCrud('/admin/poll_question', PollQuestionController::class);

        SimpleRouter::staticRoute('@^/poll/(\d+)$@', PollController::class, 'viewAction');
        SimpleRouter::staticRoute('@^/poll/(\d+)/vote$@', PollController::class, 'voteAction');
    }
}
