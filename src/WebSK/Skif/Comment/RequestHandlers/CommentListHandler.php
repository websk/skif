<?php

namespace WebSK\Skif\Comment\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\Comment\CommentUtils;
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
            return $response;
        }

        $page = $request->getParam('page', 1);

        $comments_ids_arr = CommentUtils::getCommentsIdsArrByUrl($url, $page);

        $content_html = PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Comment',
            'form_add.tpl.php',
            array('url' => $url)
        );

        $content_html .= PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Comment',
            'list.tpl.php',
            array('comments_ids_arr' => $comments_ids_arr, 'url' => $url)
        );

        $content_html .= PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Comment',
            'pager.tpl.php',
            array('url' => $url, 'page' => $page)
        );

        $response->getBody()->write($content_html);

        return $response;
    }
}
