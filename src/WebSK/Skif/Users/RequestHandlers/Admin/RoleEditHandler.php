<?php

namespace WebSK\Skif\Users\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\AdminRender;
use WebSK\Skif\LayoutDTO;
use WebSK\Skif\PhpRender;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\Skif\Users\Role;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\UI\BreadcrumbItemDTO;
use WebSK\Utils\HTTP;

/**
 * Class RoleEditHandler
 * @package WebSK\Skif\Users\RequestHandlers\Admin
 */
class RoleEditHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int|null $role_id
     * @return \Psr\Http\Message\ResponseInterface|Response
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

        $content = PhpRender::renderTemplateBySkifModule(
            'Users',
            'role_form_edit.tpl.php',
            array('role_obj' => $role_obj)
        );

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Редактирование роли пользователей');
        $layout_dto->setContentHtml($content);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Пользователи', $this->pathFor(UserListHandler::class)),
            new BreadcrumbItemDTO('Роли пользователей', $this->pathFor(RoleListHandler::class)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return AdminRender::renderLayout($response, $layout_dto);
    }
}
