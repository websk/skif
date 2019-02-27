<?php

namespace WebSK\Skif\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Utils\HTTP;
use WebSK\Views\PhpRender as PhpRender1;

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

        return PhpRender1::render($response, '/errors/error_page.tpl.php', $data);
    }
}
