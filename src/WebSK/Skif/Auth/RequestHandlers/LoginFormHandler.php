<?php

namespace WebSK\Skif\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Slim\ConfWrapper;
use WebSK\Skif\SkifPhpRender;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Skif\Auth\Auth;
use WebSK\Skif\Users\UsersRoutes;
use WebSK\Views\PhpRender as PhpRender1;

/**
 * Class LoginFormHandler
 * @package WebSK\Skif\Auth\RequestHandlers
 */
class LoginFormHandler extends \WebSK\Slim\RequestHandlers\BaseHandler
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
                $this->pathFor(UsersRoutes::ROUTE_NAME_ADMIN_USER_EDIT, ['user_id' => $current_user_id])
            );
        }

        $content = '';

        if (Auth::useSocialLogin()) {
            $content .= SkifPhpRender::renderTemplateBySkifModule(
                'Auth',
                'social_buttons.tpl.php'
            );
        }

        $content .= SkifPhpRender::renderTemplateBySkifModule(
            'Auth',
            'login_form.tpl.php'
        );

        return PhpRender1::render(
            $response,
            ConfWrapper::value('layout.main'),
            [
                'content' => $content,
                'title' => 'Вход на сайт',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => []
            ]
        );
    }
}
