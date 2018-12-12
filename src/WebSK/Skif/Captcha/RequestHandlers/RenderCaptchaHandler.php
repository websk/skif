<?php

namespace WebSK\Skif\Captcha\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\Captcha\Captcha;

class RenderCaptchaHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        ob_start();
        Captcha::render();
        $content = ob_get_clean();

        $response = $response->withHeader('Content-type', 'image/png');
        $response->getBody()->write($content);

        return $response;
    }
}
