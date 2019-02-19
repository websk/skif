<?php

namespace WebSK\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\Auth;

/**
 * Class LogoutHandler
 * @package WebSK\Auth\RequestHandlers
 */
class LogoutHandler extends \WebSK\Slim\RequestHandlers\BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        Auth::logout();

        $destination = $request->getQueryParam('destination', '/');

        return $response->withRedirect($destination);
    }
}
