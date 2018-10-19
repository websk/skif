<?php

namespace WebSK\Skif\Captcha\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Websk\Skif\Captcha\Captcha;

class CheckCaptchaAjaxHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $content = 'false';
        if (Captcha::check()) {
            $content = 'true';
        }

        $response = $response->withHeader('Content-type', 'application/json');
        $response->getBody()->write($content);

        return $response;
    }
}
