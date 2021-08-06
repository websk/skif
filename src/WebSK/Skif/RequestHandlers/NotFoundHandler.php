<?php

namespace WebSK\Skif\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\SkifPath;
use WebSK\Utils\HTTP;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class NotFoundHandler
 * @package WebSK\Skif\RequestHandlers
 */
class NotFoundHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $error_code = HTTP::STATUS_NOT_FOUND;

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

        $response = $response->withStatus($error_code);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.error'), $layout_dto);
    }
}
