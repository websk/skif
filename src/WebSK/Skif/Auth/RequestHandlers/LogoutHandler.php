<?php

namespace WebSK\Skif\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\Skif\Users\AuthUtils;

/**
 * Class LogoutHandler
 * @package WebSK\Skif\Auth\RequestHandlers
 */
class LogoutHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        AuthUtils::logout();

        $destination = $request->getQueryParam('destination', '/');

        return $response->withRedirect($destination);
    }
}
