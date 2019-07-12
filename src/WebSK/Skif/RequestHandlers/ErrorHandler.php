<?php

namespace WebSK\Skif\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Skif\SkifPhpRender;

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
        $response = $response->withStatus(StatusCode::HTTP_INTERNAL_SERVER_ERROR);

        $data = [
            'error_code' => StatusCode::HTTP_INTERNAL_SERVER_ERROR,
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
