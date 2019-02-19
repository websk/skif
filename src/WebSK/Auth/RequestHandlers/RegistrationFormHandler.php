<?php

namespace WebSK\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Config\ConfWrapper;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Slim\Router;
use WebSK\Auth\Auth;
use WebSK\Auth\Users\UsersRoutes;
use WebSK\Views\PhpRender;

/**
 * Class RegistrationFormHandler
 * @package WebSK\Auth\RequestHandlers
 */
class RegistrationFormHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $current_user_id = Auth::getCurrentUserId();
        if ($current_user_id) {
            return $response->withRedirect(
                Router::pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $current_user_id])
            );
        }

        $content = '';

        if (Auth::useSocialLogin()) {
            $content .= PhpRender::renderTemplateByModule(
                'WebSK/Auth',
                'social_buttons.tpl.php'
            );
        }

        $content .= PhpRender::renderTemplateByModule(
            'WebSK/Auth',
            'registration_form.tpl.php'
        );

        return PhpRender::render(
            $response,
            ConfWrapper::value('layout.main'),
            [
                'content' => $content,
                'title' => 'Регистрация на сайте',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => []
            ]
        );
    }
}
