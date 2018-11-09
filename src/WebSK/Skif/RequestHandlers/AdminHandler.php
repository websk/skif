<?php

namespace WebSK\Skif\RequestHandlers;

use WebSK\Skif\Auth\AuthUtils;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Utils\HTTP;
use WebSK\Skif\PhpRender;

class AdminHandler
{
    /**
     * @param Request $request
     * @param Response $response
     */
    public function __invoke(Request $request, Response $response)
    {
        if (!AuthUtils::getCurrentUserId()) {
            return PhpRender::render($response, '/layouts/layout.admin_login.tpl.php');
        }

        if (!AuthUtils::currentUserIsAdmin()) {
            return $response->withStatus(HTTP::STATUS_FORBIDDEN);
        }

        return $response->withRedirect('/admin/content/page');
    }
}
