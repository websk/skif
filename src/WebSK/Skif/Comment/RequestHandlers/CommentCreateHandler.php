<?php

namespace WebSK\Skif\Comment\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\SessionService;
use WebSK\Captcha\Captcha;
use WebSK\Skif\Comment\Comment;
use WebSK\Skif\Comment\CommentService;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;
use WebSK\Utils\Sanitize;

/**
 * Class CommentCreateHandler
 * @package WebSK\Skif\Comment\RequestHandlers
 */
class CommentCreateHandler extends BaseHandler
{
    /** @Inject */
    protected CommentService $comment_service;

    /** @Inject */
    protected SessionService $session_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $url = $request->getParsedBodyParam('url');

        if (!$url) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        if ($request->getParsedBodyParam(Captcha::CAPTCHA_FIELD_NAME) !== null) {
            if (!Captcha::checkWithMessage()) {
                return $response->withHeader('Location', $url);
            }
        }

        $comment = $request->getParsedBodyParam('comment');

        if (!$comment) {
            Messages::setError('Не указано сообщение');
            return $response->withHeader('Location', $url);
        }

        $current_user_obj = $this->session_service->getCurrentUserObj();

        $user_name = '';
        $user_email = '';

        if (!$current_user_obj) {
            $user_name = $request->getParsedBodyParam('user_name');
            if (!$user_name) {
                Messages::setError('Не указано имя');
                return $response->withHeader('Location', $url);
            }

            $user_email = $request->getParsedBodyParam('user_email');
        }

        $parent_id = $request->getParsedBodyParam('parent_id');

        $comment_obj = new Comment();
        $comment_obj->setParentId($parent_id);
        $comment_obj->setUrl($url);
        if ($user_name) {
            $user_name = Sanitize::sanitizeTagContent($user_name);
            $comment_obj->setUserName($user_name);
        }
        if ($user_email) {
            $user_email = Sanitize::sanitizeTagContent($user_email);
            $comment_obj->setUserEmail($user_email);
        }

        $comment = Sanitize::sanitizeTagContent($comment);
        $comment_obj->setComment($comment);

        $this->comment_service->save($comment_obj);

        $this->comment_service->sendEmailNotificationForComment($comment_obj);

        Messages::setMessage('Ваше сообщение добавлено');

        return $response->withHeader('Location', $url . '#comments');
    }
}
