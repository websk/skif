<?php

namespace WebSK\Skif\Users\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use Websk\Skif\Messages;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\Skif\Users\Role;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\Utils\HTTP;

/**
 * Class RoleSaveHandler
 * @package WebSK\Skif\Users\RequestHandlers\Admin
 */
class RoleSaveHandler extends BaseHandler
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

        $name = $request->getParam('name', '');
        $designation = $request->getParam('designation', '');

        $role_obj->setName($name);
        $role_obj->setDesignation($designation);
        $role_service->save($role_obj);

        Messages::setMessage('Изменения сохранены');

        return $response->withRedirect($this->pathFor(RoleListHandler::class));
    }
}
