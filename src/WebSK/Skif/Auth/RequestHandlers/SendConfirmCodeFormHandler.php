<?php

namespace WebSK\Skif\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Slim\ConfWrapper;
use WebSK\Skif\PhpRender;
use WebSK\Skif\RequestHandlers\BaseHandler;

/**
 * Class SendConfirmCodeFormHandler
 * @package WebSK\Skif\Auth\RequestHandlers
 */
class SendConfirmCodeFormHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $content = PhpRender::renderTemplateBySkifModule(
            'Users',
            'send_confirm_code_form.tpl.php'
        );

        return PhpRender::render(
            $response,
            ConfWrapper::value('layout.main'),
            array(
                'content' => $content,
                'title' => 'Подтверждение регистрации на сайте',
                'keywords' => '',
                'description' => '',
                'breadcrumbs_arr' => []
            )
        );
    }
}
