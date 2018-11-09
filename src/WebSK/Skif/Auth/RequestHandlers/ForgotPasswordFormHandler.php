<?php

namespace WebSK\Skif\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\ConfWrapper;
use WebSK\Skif\PhpRender;
use WebSK\Skif\RequestHandlers\BaseHandler;

/**
 * Class ForgotPasswordFormHandler
 * @package WebSK\Skif\Auth\RequestHandlers
 */
class ForgotPasswordFormHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $content = PhpRender::renderTemplateBySkifModule(
            'Auth',
            'forgot_password_form.tpl.php'
        );

        return PhpRender::render(
            $response,
            ConfWrapper::value('layout.main'),
            [
                'content' => $content,
                'editor_nav_arr' => [],
                'title' => 'Восстановление пароля',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => []
            ]
        );
    }
}
