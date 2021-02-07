<?php

namespace WebSK\Skif\Poll\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Skif\Poll\PollRoutes;
use WebSK\Skif\Poll\PollServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

/**
 * Class PollVoteHandler
 * @package WebSK\Skif\Poll\RequestHandlers
 */
class PollVoteHandler extends BaseHandler
{
    const POLL_COOKIE_PREFIX = 'poll_access_';

    /**
     * @param Request $request
     * @param Response $response
     * @param int $poll_id
     */
    public function __invoke(Request $request, Response $response, int $poll_id)
    {
        $poll_question_id = $request->getParam('poll_question_id');

        $poll_service = PollServiceProvider::getPollService($this->container);

        $poll_obj = $poll_service->getById($poll_id, false);
        if (!$poll_obj) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        $poll_question_service = PollServiceProvider::getPollQuestionService($this->container);

        $cookie_key = self::POLL_COOKIE_PREFIX . $poll_id;

        $redirect_url = $this->pathFor(PollRoutes::ROUTE_NAME_POLL_VIEW, ['poll_id' => $poll_id]);

        if (isset($_COOKIE[$cookie_key]) && ($_COOKIE[$cookie_key] == 'no')) {
            Messages::setError('Вы уже проголосовали ранее!');
            return $response->withRedirect($redirect_url);
        }

        if (!empty($poll_question_id)) {
            $poll_question_obj = $poll_question_service->getById($poll_question_id);

            $votes = $poll_question_obj->getVotes() + 1;
            $poll_question_obj->setVotes($votes);

            $poll_question_service->save($poll_question_obj);

            setcookie($cookie_key, 'no', time() + 3600 * 24 * 365);

            Messages::setMessage('Спасибо, ваш голос учтен!');
        } else {
            Messages::setError('Вы не проголосовали, т.к. не выбрали ответ.');
        }

        return $response->withRedirect($redirect_url);
    }
}
