<?php

namespace WebSK\Skif\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\SkifPhpRender;
use WebSK\Views\PhpRender as PhpRender1;

/**
 * Class ForgotPasswordFormHandler
 * @package WebSK\Skif\Auth\RequestHandlers
 */
class ForgotPasswordFormHandler extends \WebSK\Slim\RequestHandlers\BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $content = SkifPhpRender::renderTemplateBySkifModule(
            'Auth',
            'forgot_password_form.tpl.php'
        );

        return PhpRender1::render(
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
