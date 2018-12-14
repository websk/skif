<?php

namespace WebSK\Skif\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\SkifPhpRender;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\PhpRender as PhpRender1;

/**
 * Class SendConfirmCodeFormHandler
 * @package WebSK\Skif\Auth\RequestHandlers
 */
class SendConfirmCodeFormHandler extends \WebSK\Slim\RequestHandlers\BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $content = SkifPhpRender::renderTemplateBySkifModule(
            'Users',
            'send_confirm_code_form.tpl.php'
        );

        return PhpRender1::render(
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
