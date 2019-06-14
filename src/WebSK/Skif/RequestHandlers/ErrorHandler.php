<?php

namespace WebSK\Skif\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\SkifPhpRender;
use WebSK\Utils\HTTP;

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

        $message = $exception->getMessage();

        error_log($message . "\n" . $exception->getTraceAsString());

        return SkifPhpRender::render(
            $response,
            '/errors/error_page.tpl.php',
            $data
        );
    }
}
