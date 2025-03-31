<?php

namespace WebSK\Skif\Poll\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Poll\PollQuestionService;
use WebSK\Skif\Poll\PollService;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

/**
 * Class PollVoteHandler
 * @package WebSK\Skif\Poll\RequestHandlers
 */
class PollVoteHandler extends BaseHandler
{
    const string POLL_COOKIE_PREFIX = 'poll_access_';

    /** @Inject */
    protected PollService $poll_service;

    /** @Inject */
    protected PollQuestionService $poll_question_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $poll_id
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $poll_id): ResponseInterface
    {
        $poll_question_id = $request->getParam('poll_question_id');

        $poll_obj = $this->poll_service->getById($poll_id, false);
        if (!$poll_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $cookie_key = self::POLL_COOKIE_PREFIX . $poll_id;

        $redirect_url = $this->urlFor(PollViewHandler::class, ['poll_id' => $poll_id]);

        if (isset($_COOKIE[$cookie_key]) && ($_COOKIE[$cookie_key] == 'no')) {
            Messages::setError('Вы уже проголосовали ранее!');
            return $response->withHeader('Location', $redirect_url);
        }

        if (!empty($poll_question_id)) {
            $poll_question_obj = $this->poll_question_service->getById($poll_question_id);

            $votes = $poll_question_obj->getVotes() + 1;
            $poll_question_obj->setVotes($votes);

            $this->poll_question_service->save($poll_question_obj);

            setcookie($cookie_key, 'no', time() + 3600 * 24 * 365);

            Messages::setMessage('Спасибо, ваш голос учтен!');
        } else {
            Messages::setError('Вы не проголосовали, т.к. не выбрали ответ.');
        }

        return $response->withHeader('Location', $redirect_url);
    }
}
