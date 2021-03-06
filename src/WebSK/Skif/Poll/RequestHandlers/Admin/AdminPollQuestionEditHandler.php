<?php

namespace WebSK\Skif\Poll\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\Skif\Poll\Poll;
use WebSK\Skif\Poll\PollQuestion;
use WebSK\Skif\Poll\PollRoutes;
use WebSK\Skif\Poll\PollServiceProvider;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
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
     * @param Request $request
     * @param Response $response
     * @param int $poll_question_id
     */
    public function __invoke(Request $request, Response $response, int $poll_question_id)
    {
        $poll_question_service = PollServiceProvider::getPollQuestionService($this->container);

        $poll_question_obj = $poll_question_service->getById($poll_question_id, false);

        if (!$poll_question_obj) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
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
                        $this->pathFor(PollRoutes::ROUTE_NAME_ADMIN_POLL_LIST_AJAX),
                        $this->pathFor(
                            PollRoutes::ROUTE_NAME_ADMIN_POLL_EDIT,
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
        if ($crud_form_response instanceof Response) {
            return $crud_form_response;
        }

        $poll_service = PollServiceProvider::getPollService($this->container);
        $poll_obj = $poll_service->getById($poll_question_obj->getPollId(), false);

        $content_html = $crud_form->html();
        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($poll_question_obj->getTitle());
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO('Опросы', $this->pathFor(PollRoutes::ROUTE_NAME_ADMIN_POLL_LIST)),
            new BreadcrumbItemDTO($poll_obj->getTitle(), $this->pathFor(PollRoutes::ROUTE_NAME_ADMIN_POLL_EDIT, ['poll_id' => $poll_question_obj->getPollId()])),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);


        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
