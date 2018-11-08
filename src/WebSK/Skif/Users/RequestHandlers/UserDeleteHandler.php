<?php

namespace WebSK\Skif\Users\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use Websk\Skif\Messages;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\Utils\HTTP;

/**
 * Class UserDeleteHandler
 * @package WebSK\WebSK\Skif\Users\RequestHandlers
 */
class UserDeleteHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int|null $user_id
     * @return Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, ?int $user_id)
    {
        $user_service = UsersServiceProvider::getUserService($this->container);

        $user_obj = $user_service->getById($user_id, false);

        if (!$user_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $destination = $request->getParam('destination', '/');

        $user_service->delete($user_obj);

        Messages::setMessage('Пользователь ' . $user_obj->getName() . ' был успешно удален');

        return $response->withRedirect($destination);
    }
}
