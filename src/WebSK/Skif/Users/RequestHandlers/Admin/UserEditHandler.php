<?php

namespace WebSK\Skif\Users\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\AdminRender;
use WebSK\Skif\LayoutDTO;
use WebSK\Skif\PhpRender;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\UI\BreadcrumbItemDTO;
use WebSK\Utils\HTTP;

/**
 * Class UserEditHandler
 * @package WebSK\WebSK\Skif\Users\RequestHandlers\Admin
 */
class UserEditHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int|null $user_id
     * @return \Psr\Http\Message\ResponseInterface|Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, ?int $user_id)
    {
        $user_service = UsersServiceProvider::getUserService($this->container);

        if ($user_id != 'new') {
            $user_obj = $user_service->getById($user_id, false);

            if (!$user_obj) {
                return $response->withStatus(HTTP::STATUS_NOT_FOUND);
            }
        }

        $content = '';

        $user_roles_ids_arr = $user_service->getRoleIdsArrByUserId($user_id);

        $content .= PhpRender::renderTemplateBySkifModule(
            'Users',
            'profile_form_edit.tpl.php',
            [
                'user_id' => $user_id,
                'user_roles_ids_arr' => $user_roles_ids_arr
            ]
        );

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Редактирование профиля');
        $layout_dto->setContentHtml($content);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Пользователи', '/admin/users'),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return AdminRender::renderLayout($response, $layout_dto);
    }
}
