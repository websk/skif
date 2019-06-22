<?php

namespace WebSK\Skif\Comment\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\Comment\CommentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\PhpRender;

/**
 * Class CommentListHandler
 * @package WebSK\Skif\Comment\RequestHandlers
 */
class CommentListHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $url = $request->getParam('url');

        if (!$url) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        $content_html = PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Comment',
            'form_add.tpl.php',
            ['url' => $url]
        );

        $page = $request->getParam('page', 1);

        $comment_service = CommentServiceProvider::getCommentService($this->container);
        $comments_ids_arr = $comment_service->getCommentsIdsArrByUrl($url, $page);

        $content_html .= PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Comment',
            'list.tpl.php',
            [
                'comments_ids_arr' => $comments_ids_arr,
                'url' => $url
            ]
        );

        $content_html .= PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Comment',
            'pager.tpl.php',
            [
                'url' => $url,
                'page' => $page,
                'count_comments' => $comment_service->getCountCommentsByUrl($url),
                'message_to_page' => ConfWrapper::value('comments.message_to_page', 20)
            ]
        );

        $response->getBody()->write($content_html);

        return $response;
    }
}
