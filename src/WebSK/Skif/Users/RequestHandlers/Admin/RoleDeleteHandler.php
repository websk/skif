<?php

namespace WebSK\Skif\Users\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use Websk\Skif\Messages;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\Skif\Users\Role;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\Skif\Users\UsersUtils;
use WebSK\Utils\HTTP;

class RoleDeleteHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int|null $role_id
     * @return Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, ?int $role_id)
    {
        $role_service = UsersServiceProvider::getRoleService($this->container);

        if ($role_id == 'new') {
            $role_obj = new Role;
        } else {
            $role_obj = $role_service->getById($role_id, false);
            if (!$role_obj) {
                return $response->withStatus(HTTP::STATUS_NOT_FOUND);
            }
        }

        $user_ids_arr = UsersUtils::getUsersIdsArr($role_id);

        if (!empty($user_ids_arr)) {
            Messages::setError('Нельзя удалить роль ' . $role_obj->getName() . ', т.к. она назначена пользователям');
            return $response->withRedirect($this->pathFor(RoleListHandler::class));
        }

        $role_service->delete($role_obj);

        Messages::setMessage('Роль ' . $role_obj->getName() . ' была успешно удалена');

        return $response->withRedirect($this->pathFor(RoleListHandler::class));
    }
}
