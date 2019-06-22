<?php

namespace WebSK\Skif\Comment\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Captcha\Captcha;
use WebSK\Skif\Comment\Comment;
use WebSK\Skif\Comment\CommentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

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
                $response->withRedirect($url);
            }
        }

        $comment = $request->getParsedBodyParam('comment');

        if (!$comment) {
            Messages::setError('Не указано сообщение');
            $response->withRedirect($url);
        }

        $user_name = $request->getParsedBodyParam('user_name');
        if (!$user_name) {
            Messages::setError('Не указано имя');
            $response->withRedirect($url);
        }


        $user_email = $request->getParsedBodyParam('user_email');
        $parent_id = $request->getParsedBodyParam('parent_id');

        $comment_service = CommentServiceProvider::getCommentService($this->container);

        $comment_obj = new Comment();
        $comment_obj->setParentId($parent_id);
        $comment_obj->setUrl($url);
        if ($user_name) {
            $comment_obj->setUserName($user_name);
        }
        if ($user_email) {
            $comment_obj->setUserEmail($user_email);
        }
        $comment_obj->setComment($comment);
        $comment_service->save($comment_obj);

        Messages::setMessage('Ваше сообщение добавлено');

        return $response->withRedirect($url . '#comments');
    }
}
