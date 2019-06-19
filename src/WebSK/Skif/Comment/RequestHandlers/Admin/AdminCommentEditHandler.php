<?php

namespace WebSK\Skif\Comment\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class AdminCommentEditHandler
 * @package WebSK\Skif\Comment\RequestHandlers\Admin
 */
class AdminCommentEditHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int $comment_id
     */
    public function __invoke(Request $request, Response $response, int $comment_id)
    {

    }
}
