<?php

namespace WebSK\Skif\Comment\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\SessionService;
use WebSK\Auth\User\UserService;
use WebSK\Config\ConfWrapper;
use WebSK\CRUD\CRUD;
use WebSK\Skif\Comment\CommentRoutes;
use WebSK\Skif\Comment\CommentService;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\PhpRender;

/**
 * Class CommentListHandler
 * @package WebSK\Skif\Comment\RequestHandlers
 */
class CommentListHandler extends BaseHandler
{

    const string PARAM_URL = 'url';
    const string PARAM_PAGE = 'page';

    const int DEFAULT_PAGE = 1;

    /** @Inject */
    protected CommentService $comment_service;

    /** @Inject */
    protected SessionService $session_service;

    /** @Inject */
    protected UserService $user_service;

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $url = $request->getParam(self::PARAM_URL);

        if (!$url) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $current_user_obj = $this->session_service->getCurrentUserObj();

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

        $comments_ids_arr = $this->comment_service->getCommentsIdsArrByUrl($url, $page);

        $content_html .= PhpRender::renderTemplateForModuleNamespace(
            CommentRoutes::NAMESPACE_DIR,
            'list.tpl.php',
            [
                'comments_ids_arr' => $comments_ids_arr,
                'url' => $url,
                'current_user_obj' => $current_user_obj,
                'current_user_is_admin' => $current_user_obj ? $this->user_service->hasRoleAdminByUserId($current_user_obj->getId()) : false,
                'comment_service' => $this->comment_service,
                'crud_service' => $this->crud_service
            ]
        );

        $content_html .= PhpRender::renderTemplateForModuleNamespace(
            CommentRoutes::NAMESPACE_DIR,
            'pager.tpl.php',
            [
                'url' => $url,
                'page' => $page,
                'count_comments' => $this->comment_service->getCountCommentsByUrl($url),
                'message_to_page' => ConfWrapper::value(CommentService::CONFIG_MESSAGE_TO_PAGE, CommentService::DEFAULT_MESSAGE_TO_PAGE)
            ]
        );

        $response->getBody()->write($content_html);

        return $response;
    }
}
