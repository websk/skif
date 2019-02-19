<?php

namespace WebSK\Auth\Users\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\Users\UsersRoutes;
use WebSK\Auth\Users\UsersUtils;

/**
 * Class UserCreatePasswordHandler
 * @package WebSK\Auth\Users\RequestHandlers
 */
class UserCreatePasswordHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int $user_id
     * @return Response
     */
    public function __invoke(Request $request, Response $response, int $user_id)
    {
        $destination = $request->getQueryParam('destination', $this->pathFor(UsersRoutes::ROUTE_NAME_USER_EDIT, ['user_id' => $user_id]));

        $new_password = UsersUtils::createAndSendPasswordToUser($user_id);

        Messages::setMessage('Новый пароль' . $new_password);

        return $response->withRedirect($destination);
    }
}
