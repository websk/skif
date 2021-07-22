<?php

namespace WebSK\Skif\Poll\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\Poll\PollServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\HTTP;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class PollViewHandler
 * @package WebSK\Skif\Poll\RequestHandlers
 */
class PollViewHandler extends BaseHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $poll_id
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $poll_id)
    {
        $poll_service = PollServiceProvider::getPollService($this->container);

        $poll_obj = $poll_service->getById($poll_id, false);

        if (!$poll_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $poll_question_service = PollServiceProvider::getPollQuestionService($this->container);

        $content_html = PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Poll',
            'view.tpl.php',
            [
                'poll_id' => $poll_id,
                'poll_question_service' => $poll_question_service
            ]
        );

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($poll_obj->getTitle());
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', '/'),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);


        return PhpRender::renderLayout($response, ConfWrapper::value('layout.main'), $layout_dto);
    }
}
