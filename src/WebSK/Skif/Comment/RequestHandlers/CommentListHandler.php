<?php

namespace WebSK\Skif\Comment\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\AuthServiceProvider;
use WebSK\Auth\User\UserServiceProvider;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\Comment\CommentRoutes;
use WebSK\Skif\Comment\CommentService;
use WebSK\Skif\Comment\CommentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\HTTP;
use WebSK\Views\PhpRender;

/**
 * Class CommentListHandler
 * @package WebSK\Skif\Comment\RequestHandlers
 */
class CommentListHandler extends BaseHandler
{

    const PARAM_URL = 'url';
    const PARAM_PAGE = 'page';

    const DEFAULT_PAGE = 1;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $url = $request->getParam(self::PARAM_URL);

        if (!$url) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $auth_service = AuthServiceProvider::getSessionService($this->container);
        $user_service = UserServiceProvider::getUserService($this->container);

        $current_user_obj = $auth_service->getCurrentUserObj();

        $content_html = PhpRender::renderTemplateForModuleNamespace(
            CommentRoutes::NAMESPACE_DIR,
            'form_add.tpl.php',
            [
                'url' => $url,
                'current_user_obj' => $current_user_obj,
                'no_add_comments_for_unregistered_users' => ConfWrapper::value(CommentService::CONFIG_NO_ADD_COMMENTS_FOR_UNREGISTERED_USERS)
            ]
        );

        $page = $request->getParam(self::PARAM_PAGE, self::DEFAULT_PAGE);

        $comment_service = CommentServiceProvider::getCommentService($this->container);
        $comments_ids_arr = $comment_service->getCommentsIdsArrByUrl($url, $page);

        $content_html .= PhpRender::renderTemplateForModuleNamespace(
            CommentRoutes::NAMESPACE_DIR,
            'list.tpl.php',
            [
                'comments_ids_arr' => $comments_ids_arr,
                'url' => $url,
                'current_user_obj' => $current_user_obj,
                'current_user_is_admin' => $current_user_obj ? $user_service->hasRoleAdminByUserId($current_user_obj->getId()) : false,
                'comment_service' => $comment_service
            ]
        );

        $content_html .= PhpRender::renderTemplateForModuleNamespace(
            CommentRoutes::NAMESPACE_DIR,
            'pager.tpl.php',
            [
                'url' => $url,
                'page' => $page,
                'count_comments' => $comment_service->getCountCommentsByUrl($url),
                'message_to_page' => ConfWrapper::value(CommentService::CONFIG_MESSAGE_TO_PAGE, CommentService::DEFAULT_MESSAGE_TO_PAGE)
            ]
        );

        $response->getBody()->write($content_html);

        return $response;
    }
}
