<?php

namespace WebSK\Skif\RequestHandlers;

use WebSK\Skif\Auth\Auth;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\SkifPhpRender;
use WebSK\Views\PhpRender as PhpRender1;

/**
 * Class AdminHandler
 * @package WebSK\Skif\RequestHandlers
 */
class AdminHandler
{
    /**
     * @param Request $request
     * @param Response $response
     */
    public function __invoke(Request $request, Response $response)
    {
        if (!Auth::getCurrentUserId()) {
            return PhpRender1::render($response, '/layouts/layout.admin_login.tpl.php');
        }

        return $response->withRedirect('/admin/content/page');
    }
}
