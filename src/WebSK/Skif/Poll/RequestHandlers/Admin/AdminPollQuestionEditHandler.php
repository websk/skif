<?php

namespace WebSK\Skif\Poll\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\Skif\Poll\Poll;
use WebSK\Skif\Poll\PollQuestion;
use WebSK\Skif\Poll\PollServiceProvider;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\HTTP;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminPollQuestionEditHandler
 * @package WebSK\Skif\Poll\RequestHandlers\Admin
 */
class AdminPollQuestionEditHandler extends BaseHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $poll_question_id
     * @return ResponseInterface
     * @throws \ReflectionException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $poll_question_id): ResponseInterface
    {
        $poll_question_service = PollServiceProvider::getPollQuestionService($this->container);

        $poll_question_obj = $poll_question_service->getById($poll_question_id, false);

        if (!$poll_question_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $crud_form = CRUDServiceProvider::getCrud($this->container)->createForm(
            'poll_question_edit',
            $poll_question_obj,
            [
                new CRUDFormRow(
                    'Опрос',
                    new CRUDFormWidgetReferenceAjax(
                        PollQuestion::_POLL_ID,
                        Poll::class,
                        Poll::_TITLE,
                        $this->pathFor(AdminPollListAjaxHandler::class),
                        $this->pathFor(
                            AdminPollEditHandler::class,
                            ['poll_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER]
                        )
                    )
                ),
                new CRUDFormRow('Заголовок', new CRUDFormWidgetInput(PollQuestion::_TITLE)),
                new CRUDFormRow('Сортировка', new CRUDFormWidgetInput(PollQuestion::_WEIGHT)),
                new CRUDFormRow('Проголосовало', new CRUDFormWidgetInput(PollQuestion::_VOTES)),
            ]
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $poll_service = PollServiceProvider::getPollService($this->container);
        $poll_obj = $poll_service->getById($poll_question_obj->getPollId());

        $content_html = $crud_form->html();
        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($poll_question_obj->getTitle());
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO('Опросы', $this->pathFor(AdminPollListHandler::class)),
            new BreadcrumbItemDTO($poll_obj->getTitle(), $this->pathFor(AdminPollEditHandler::class, ['poll_id' => $poll_question_obj->getPollId()])),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
