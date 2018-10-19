<?php

namespace WebSK\Skif\RequestHandlers;

use Skif\Http;
use Skif\Users\AuthUtils;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;

class AdminHandler
{
    /**
     * @param Request $request
     * @param Response $response
     */
    public function __invoke(Request $request, Response $response)
    {
        if (AuthUtils::getCurrentUserId()) {
            if (AuthUtils::currentUserIsAdmin()) {
                Http::redirect('/admin/content/page');
            }

            Http::exit403();
        }

        $php_renderer = new PhpRenderer(__DIR__ .'/../../../../views');

        return $php_renderer->render($response, '/layouts/layout.admin_login.tpl.php');
    }
}
