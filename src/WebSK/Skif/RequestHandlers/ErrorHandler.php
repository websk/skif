<?php

namespace WebSK\Skif\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Auth\Auth;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\SkifPath;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

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
        $error_code = StatusCode::HTTP_INTERNAL_SERVER_ERROR;

        $extra_message = 'Ошибка. 500 Internal Server Error';

        $message = 'Что-то пошло не так';
        if (Auth::currentUserIsAdmin()) {
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


        $response = $response->withStatus($error_code);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.error'), $layout_dto);
    }
}
