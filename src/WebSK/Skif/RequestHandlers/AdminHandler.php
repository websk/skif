<?php

namespace WebSK\Skif\RequestHandlers;

use WebSK\Config\ConfWrapper;
use WebSK\Auth\Auth;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Views\PhpRender;

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
            return PhpRender::render($response, '/layouts/layout.admin_login.tpl.php');
        }

        return $response->withRedirect(ConfWrapper::value('skif_main_page'));
    }
}
