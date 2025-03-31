<?php

namespace WebSK\Skif\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Handlers\ErrorHandler;
use Throwable;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\SkifPath;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class NotFoundHandler
 * @package WebSK\Skif\RequestHandlers
 */
class NotFoundHandler extends ErrorHandler
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

        $error_code = StatusCodeInterface::STATUS_NOT_FOUND;

        $extra_message = 'Страница не найдена';
        $message = 'Возможные причины: неправильно набран адрес, документ был удален, документ был переименован';

        $content_html = '<div class="alert alert-warning" role="alert">';
        $content_html .= '<h4><span class="glyphicon glyphicon-exclamation-sign"></span> ' . $extra_message . '</h4>';
        $content_html .= '<div style="white-space: pre-wrap; font-size: larger">' . $message . '</div>';
        $content_html .= '</div>';

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
