<?php

namespace WebSK\Skif\Users\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\Auth\AuthUtils;
use WebSK\Utils\HTTP;

/**
 * Class CurrentUserHasRightToEditUser
 * @package WebSK\WebSK\Skif\Users\Middleware
 */
class CurrentUserHasRightToEditUser
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        $user_id = $request->getAttribute('routeInfo')[2]['user_id'] ?? null;

        if (!isset($user_id)) {
            $response = $next($request, $response);

            return $response;
        }

        $user_id = (int)$user_id;

        $current_user_id = AuthUtils::getCurrentUserId();

        if (($current_user_id != $user_id) && !AuthUtils::currentUserIsAdmin()) {
            return $response->withStatus(HTTP::STATUS_FORBIDDEN);
        }

        $response = $next($request, $response);

        return $response;
    }
}

