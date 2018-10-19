<?php

namespace WebSK\Skif\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;

class ErrorHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param \Exception $exception
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response, $exception)
    {
        $response = $response->withStatus(500);

        $data = [
            'error_code' => 500,
            'response' => $response
        ];

        $php_renderer = new PhpRenderer(__DIR__ .'/../../../../views');

        return $php_renderer->render($response, '/errors/error_page.tpl.php', $data);
    }
}
