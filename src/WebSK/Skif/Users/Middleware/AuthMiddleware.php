<?php

namespace WebSK\Skif\Users\Middleware;

use Dflydev\FigCookies\FigResponseCookies;
use Slim\Http\Request;
use Slim\Http\Response;
use VitrinaTV\Core\Auth\Auth;

/**
 * Class AuthMiddleware
 * @package VitrinaTV\Core\Auth\Middleware
 */
class AuthMiddleware
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        $user_id = Auth::getUserIdFromSessionCookies($request->getCookieParams());
        if ($user_id) {
            Auth::setCurrentUserId($user_id);
            $user_session_id = Auth::getSessionIdFromCookies($request->getCookieParams());
            Auth::storeUserSession($user_id, $user_session_id);
            $cookie_obj = Auth::createAuthCookie($user_session_id);
            $response = FigResponseCookies::set($response, $cookie_obj);
        }

        $response = $next($request, $response);

        return $response;
    }
}
