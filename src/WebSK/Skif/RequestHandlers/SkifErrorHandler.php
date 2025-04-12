<?php

namespace WebSK\Skif\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\SkifPath;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;
use Slim\Handlers\ErrorHandler;

/**
 * Class ErrorHandler
 * @package WebSK\Skif\RequestHandlers
 */
class SkifErrorHandler extends ErrorHandler
{

    /**
     * @param ServerRequestInterface $request
     * @param Throwable $exception
     * @param bool $displayErrorDetails
     * @param bool $logErrors
     * @param bool $logErrorDetails
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        $this->displayErrorDetails = $displayErrorDetails;
        $this->logErrors = $logErrors;
        $this->logErrorDetails = $logErrorDetails;
        $this->request = $request;
        $this->exception = $exception;
        $this->method = $request->getMethod();
        $this->statusCode = $this->determineStatusCode();
        if ($this->contentType === null) {
            $this->contentType = $this->determineContentType($request);
        }

        if ($logErrors) {
            $this->writeToErrorLog();
        }

        $error_code = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;

        $extra_message = 'Ошибка. 500 Internal Server Error';

        $message = 'Что-то пошло не так';
        if ($displayErrorDetails) {
            $message = $exception->getMessage();
        }

        $content_html = '<div class="alert alert-danger" role="alert">';
        $content_html .= '<h4><span class="glyphicon glyphicon-exclamation-sign"></span> ' . $extra_message . '</h4>';
        $content_html .= '<div style="white-space: pre-wrap; font-size: larger">' . $message . '</div>';
        $content_html .= '</div>';

        error_log($exception->getMessage() . "\n" . $exception->getTraceAsString());

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($error_code);
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        $response = $this->responseFactory->createResponse($this->statusCode);
        $response = $response->withStatus($error_code);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.error'), $layout_dto);
    }

}
