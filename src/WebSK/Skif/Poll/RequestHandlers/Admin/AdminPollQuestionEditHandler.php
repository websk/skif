<?php

namespace WebSK\Skif\Poll\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\Logger\LoggerRender;
use WebSK\Skif\Poll\Poll;
use WebSK\Skif\Poll\PollQuestion;
use WebSK\Skif\Poll\PollQuestionService;
use WebSK\Skif\Poll\PollService;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\NavTabItemDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminPollQuestionEditHandler
 * @package WebSK\Skif\Poll\RequestHandlers\Admin
 */
class AdminPollQuestionEditHandler extends BaseHandler
{
    /** @Inject */
    protected PollService $poll_service;

    /** @Inject */
    protected PollQuestionService $poll_question_service;

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $poll_question_id
     * @return ResponseInterface
     * @throws \ReflectionException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $poll_question_id): ResponseInterface
    {
        $poll_question_obj = $this->poll_question_service->getById($poll_question_id, false);

        if (!$poll_question_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $crud_form = $this->crud_service->createForm(
            'poll_question_edit',
            $poll_question_obj,
            [
                new CRUDFormRow(
                    'Опрос',
                    new CRUDFormWidgetReferenceAjax(
                        PollQuestion::_POLL_ID,
                        Poll::class,
                        Poll::_TITLE,
                        $this->urlFor(AdminPollListAjaxHandler::class),
                        $this->urlFor(
                            AdminPollEditHandler::class,
                            ['poll_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER]
                        )
                    )
                ),
                new CRUDFormRow('Заголовок', new CRUDFormWidgetInput(PollQuestion::_TITLE)),
                new CRUDFormRow('Проголосовало', new CRUDFormWidgetInput(PollQuestion::_VOTES)),
            ]
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $poll_obj = $this->poll_service->getById($poll_question_obj->getPollId());

        $content_html = $crud_form->html();
        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($poll_question_obj->getTitle());
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO('Опросы', $this->urlFor(AdminPollListHandler::class)),
            new BreadcrumbItemDTO($poll_obj->getTitle(), $this->urlFor(AdminPollEditHandler::class, ['poll_id' => $poll_question_obj->getPollId()])),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        $layout_dto->setNavTabsDtoArr(
            [
                new NavTabItemDTO(
                    'Редактирование',
                    $this->urlFor(
                        self::class,
                        ['poll_question_id' => $poll_question_id]
                    )
                ),
                new NavTabItemDTO('Журнал', LoggerRender::getLoggerLinkForEntityObj($poll_question_obj), '_blank'),
            ]
        );

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
