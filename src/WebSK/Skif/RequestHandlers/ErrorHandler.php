<?php

namespace WebSK\Skif\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Utils\HTTP;
use WebSK\Views\PhpRender;
use WebSK\Views\ViewsPath;

/**
 * Class ErrorHandler
 * @package WebSK\Skif\RequestHandlers
 */
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
        $response = $response->withStatus(HTTP::STATUS_INTERNAL_SERVER_ERROR);

        $data = [
            'error_code' => HTTP::STATUS_INTERNAL_SERVER_ERROR,
            'response' => $response
        ];

        return PhpRender::render(
            $response,
            ViewsPath::getFullTemplatePath('/views/errors/error_page.tpl.php'),
            $data
        );
    }
}
