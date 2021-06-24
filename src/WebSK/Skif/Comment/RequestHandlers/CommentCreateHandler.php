<?php

namespace WebSK\Skif\Comment\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Auth\AuthServiceProvider;
use WebSK\Captcha\Captcha;
use WebSK\Skif\Comment\Comment;
use WebSK\Skif\Comment\CommentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;
use WebSK\Utils\Sanitize;

/**
 * Class CommentCreateHandler
 * @package WebSK\Skif\Comment\RequestHandlers
 */
class CommentCreateHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $url = $request->getParsedBodyParam('url');

        if (!$url) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        if ($request->getParsedBodyParam(Captcha::CAPTCHA_FIELD_NAME) !== null) {
            if (!Captcha::checkWithMessage()) {
                return $response->withRedirect($url);
            }
        }

        $comment = $request->getParsedBodyParam('comment');

        if (!$comment) {
            Messages::setError('Не указано сообщение');
            return $response->withRedirect($url);
        }

        $auth_service = AuthServiceProvider::getSessionService($this->container);

        $current_user_obj = $auth_service->getCurrentUserObj();

        $user_name = '';
        $user_email = '';

        if (!$current_user_obj) {
            $user_name = $request->getParsedBodyParam('user_name');
            if (!$user_name) {
                Messages::setError('Не указано имя');
                return $response->withRedirect($url);
            }

            $user_email = $request->getParsedBodyParam('user_email');
        }

        $parent_id = $request->getParsedBodyParam('parent_id');

        $comment_service = CommentServiceProvider::getCommentService($this->container);

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

        $comment_service->save($comment_obj);

        $comment_service->sendEmailNotificationForComment($comment_obj);

        Messages::setMessage('Ваше сообщение добавлено');

        return $response->withRedirect($url . '#comments');
    }
}
