<?php

namespace WebSK\Skif\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;

class NotFoundHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $response = $response->withStatus(404);

        $data = [
            'error_code' => 404,
            'response' => $response
        ];

        $php_renderer = new PhpRenderer(__DIR__ .'/../../../../views');

        return $php_renderer->render($response, '/errors/error_page.tpl.php', $data);
    }
}
