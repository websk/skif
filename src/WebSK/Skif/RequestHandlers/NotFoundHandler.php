<?php

namespace WebSK\Skif\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Skif\SkifPhpRender;
use WebSK\Utils\HTTP;

class NotFoundHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $response = $response->withStatus(StatusCode::HTTP_NOT_FOUND);

        $error_messages_arr = array(
            HTTP::STATUS_NOT_FOUND => array(
                'title' => 'документ не найден',
                'messages' => ['неправильно набран адрес', 'документ был удален', 'документ был перемещен', 'документ был переименован']
            ),
            HTTP::STATUS_FORBIDDEN => [
                'title' => 'доступ запрещен'
            ],
        );

        $data = [
            'error_code' => StatusCode::HTTP_NOT_FOUND,
            'response' => $response
        ];

        return SkifPhpRender::render(
            $response,
            '/errors/error_page.tpl.php',
            $data
        );
    }
}
