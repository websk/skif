<?php

namespace WebSK\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Config\ConfWrapper;
use WebSK\Views\PhpRender;

/**
 * Class SendConfirmCodeFormHandler
 * @package WebSK\Auth\RequestHandlers
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
        $content = PhpRender::renderTemplateByModule(
            'WebSK/Auth',
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
