<?php

namespace WebSK\Skif\Poll\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\Poll\PollQuestionService;
use WebSK\Skif\Poll\PollService;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class PollViewHandler
 * @package WebSK\Skif\Poll\RequestHandlers
 */
class PollViewHandler extends BaseHandler
{
    /** @Inject */
    protected PollService $poll_service;

    /** @Inject */
    protected PollQuestionService $poll_question_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $poll_id
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $poll_id): ResponseInterface
    {
        $poll_obj = $this->poll_service->getById($poll_id, false);

        if (!$poll_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $content_html = PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Poll',
            'view.tpl.php',
            [
                'poll_id' => $poll_id,
                'poll_question_service' => $this->poll_question_service
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
