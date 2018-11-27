<?php

namespace WebSK\Skif\Users\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\AdminRender;
use WebSK\UI\LayoutDTO;
use WebSK\Skif\PhpRender;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\Skif\Users\Role;
use WebSK\Skif\Users\UsersRoutes;
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
    public function __invoke(Request $request, Response $response, ?int $role_id = null)
    {
        $role_service = UsersServiceProvider::getRoleService($this->container);

        if (is_null($role_id)) {
            $role_obj = new Role;
            $save_handler_url = $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_ROLE_ADD);
        } else {
            $role_obj = $role_service->getById($role_id, false);
            if (!$role_obj) {
                return $response->withStatus(HTTP::STATUS_NOT_FOUND);
            }

            $save_handler_url = $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_ROLE_UPDATE, ['role_id' => $role_id]);
        }

        $content = PhpRender::renderTemplateBySkifModule(
            'Users',
            'role_form_edit.tpl.php',
            ['role_obj' => $role_obj, 'save_handler_url' => $save_handler_url]
        );

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Редактирование роли пользователей');
        $layout_dto->setContentHtml($content);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', '/admin'),
            new BreadcrumbItemDTO('Пользователи', $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_LIST)),
            new BreadcrumbItemDTO('Роли пользователей', $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_ROLE_LIST)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return AdminRender::renderLayout($response, $layout_dto);
    }
}
