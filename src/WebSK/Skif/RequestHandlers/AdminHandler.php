<?php

namespace WebSK\Skif\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\Auth;
use WebSK\Skif\SkifPath;
use WebSK\Skif\SkifPhpRender;

/**
 * Class AdminHandler
 * @package WebSK\Skif\RequestHandlers
 */
class AdminHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
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
