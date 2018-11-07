<?php

namespace WebSK\Skif\Users\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use VitrinaTV\Core\Auth\Auth;
use VitrinaTV\Core\Auth\AuthRoutes;
use VitrinaTV\Core\Auth\RequestHandlers\LoginHandler;
use WebSK\Skif\Router;

/**
 * Class CurrentUserHasAnyOfPermissions
 * @package VitrinaTV\Admin\MiddleWare
 */
class CurrentUserHasAnyOfPermissions
{
    /** @var array */
    protected $permissions_arr;

    /**
     * CurrentUserHasAnyOfPermissions constructor.
     * @param array $permissions_arr
     */
    public function __construct(array $permissions_arr)
    {
        $this->permissions_arr = $permissions_arr;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        /*
        if (!Auth::currentUserHasAnyOfPermissions($this->permissions_arr)) {
            $redirect_url = Router::pathFor(
                AuthRoutes::ROUTE_NAME_AUTH_LOGIN,
                [],
                [LoginHandler::PARAM_SUCCESS_REDIRECT_URL => $request->getUri()->getPath()]
            );
            return $response->withRedirect($redirect_url);
        }
        */
        $response = $next($request, $response);

        return $response;
    }
}
