<?php

namespace WebSK\Skif\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\Auth\AuthRoutes;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\Skif\Auth\AuthUtils;

/**
 * Class LoginHandler
 * @package WebSK\Skif\Auth\RequestHandlers
 */
class LoginHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        if (is_null($request->getParam('email')) || is_null($request->getParam('password'))) {
            return $response->withRedirect($this->pathFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM));
        }

        $save_auth = ((int)$request->getParam('save_auth') == 1) ? true : false;
        AuthUtils::doLogin($request->getParam('email'), $request->getParam('password'), $save_auth);

        $destination = $request->getParam('destination', '/');

        return $response->withRedirect($destination);
    }
}
