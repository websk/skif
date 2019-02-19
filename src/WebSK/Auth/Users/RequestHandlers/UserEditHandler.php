<?php

namespace WebSK\Auth\Users\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Config\ConfWrapper;
use WebSK\Views\LayoutDTO;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\Users\User;
use WebSK\Auth\Users\UsersRoutes;
use WebSK\Auth\Users\UsersServiceProvider;
use WebSK\Utils\HTTP;
use WebSK\Views\PhpRender;

/**
 * Class UserEditHandler
 * @package WebSK\Auth\Users\RequestHandlers
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
    public function __invoke(Request $request, Response $response, ?int $user_id = null)
    {
        $user_service = UsersServiceProvider::getUserService($this->container);

        if (is_null($user_id)) {
            $user_obj = new User();
            $save_handler_url = $this->pathFor(UsersRoutes::ROUTE_NAME_USER_ADD);
            $user_roles_ids_arr = [];
        } else {
            $user_obj = $user_service->getById($user_id, false);

            if (!$user_obj) {
                return $response->withStatus(HTTP::STATUS_NOT_FOUND);
            }

            $save_handler_url = $this->pathFor(UsersRoutes::ROUTE_NAME_USER_UPDATE, ['user_id' => $user_id]);
            $user_roles_ids_arr = $user_service->getRoleIdsArrByUserId($user_id);
        }

        $content = '';

        $content .= PhpRender::renderTemplateByModule(
            'WebSK/Auth/Users',
            'user_form_edit.tpl.php',
            [
                'user_obj' => $user_obj,
                'user_roles_ids_arr' => $user_roles_ids_arr,
                'save_handler_url' => $save_handler_url
            ]
        );

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Редактирование профиля');
        $layout_dto->setContentHtml($content);

        return PhpRender::render(
            $response,
            ConfWrapper::value('layout.main'),
            [
                'content' => $content,
                'editor_nav_arr' => [],
                'title' => 'Редактирование профиля',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => []
            ]
        );
    }
}
