<?php

namespace WebSK\Skif;

use Skif\Http;
use Skif\PhpTemplate;
use Skif\Users\AuthUtils;
use Slim\Http\Request;
use Slim\Http\Response;

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

        echo PhpTemplate::renderTemplate(
            'layouts/layout.admin_login.tpl.php'
        );
    }
}
