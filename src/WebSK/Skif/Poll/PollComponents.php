<?php

namespace WebSK\Skif\Poll;

use WebSK\Slim\Container;
use WebSK\Views\PhpRender;

/**
 * Class PollComponents
 * @package WebSK\Skif\Poll
 */
class PollComponents
{

    /**
     * @param int|null $poll_id
     * @return string
     */
    public static function renderBlockByPollId(?int $poll_id = null): string
    {
        $container = Container::self();
        $poll_service = PollServiceProvider::getPollService($container);

        if (!$poll_id) {
            $poll_id = $poll_service->getDefaultPollId();
        }

        if (!$poll_id) {
            return '';
        }

        $poll_question_service = PollServiceProvider::getPollQuestionService($container);

        $poll_obj = $poll_service->getById($poll_id);

        return PhpRender::renderTemplateInViewsDir(
            'block.tpl.php',
            [
                'poll_obj' => $poll_obj,
                'poll_service' => $poll_service,
                'poll_question_service' => $poll_question_service
            ]
        );
    }
}
