<?php

namespace WebSK\Skif\RequestHandlers;

use WebSK\Auth\Auth;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\SkifPath;
use WebSK\Skif\SkifPhpRender;

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
            return SkifPhpRender::render(
                $response,
                SkifPhpRender::ADMIN_LAYOUT_LOGIN_TEMPLATE
            );
        }

        return $response->withRedirect(SkifPath::getMainPage());
    }
}
