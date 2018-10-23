<?php

namespace WebSK\Skif\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\HTTP;
use WebSK\Skif\PhpRender;

class NotFoundHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $response = $response->withStatus(HTTP::STATUS_NOT_FOUND);

        $data = [
            'error_code' => HTTP::STATUS_NOT_FOUND,
            'response' => $response
        ];

        return PhpRender::render($response, '/errors/error_page.tpl.php', $data);
    }
}
