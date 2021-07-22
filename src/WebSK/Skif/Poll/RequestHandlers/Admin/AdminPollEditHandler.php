<?php

namespace WebSK\Skif\Poll\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormInvisibleRow;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetDate;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetRadios;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInvisible;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\Skif\Poll\Poll;
use WebSK\Skif\Poll\PollQuestion;
use WebSK\Skif\Poll\PollRoutes;
use WebSK\Skif\Poll\PollServiceProvider;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\HTTP;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminPollEditHandler
 * @package WebSK\Skif\Poll\RequestHandlers\Admin
 */
class AdminPollEditHandler extends BaseHandler
{
    const FILTER_NAME_POLL_ID = 'poll_id';

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

        $crud_form = CRUDServiceProvider::getCrud($this->container)->createForm(
            'poll_edit',
            $poll_obj,
            [
                new CRUDFormRow('Заголовок', new CRUDFormWidgetInput(Poll::_TITLE)),
                new CRUDFormRow('По умолчанию', new CRUDFormWidgetRadios(Poll::_IS_DEFAULT, [false => 'Нет', true => 'Да'])),
                new CRUDFormRow('Опубликовано', new CRUDFormWidgetRadios(Poll::_IS_PUBLISHED, [false => 'Нет', true => 'Да'])),
                new CRUDFormRow('Показывать с', new CRUDFormWidgetDate(Poll::_PUBLISHED_AT)),
                new CRUDFormRow('Показывать по', new CRUDFormWidgetDate(Poll::_UNPUBLISHED_AT)),
            ]
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();

        $poll_question_obj = new PollQuestion();
        $poll_question_obj->setPollId($poll_id);

        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            PollQuestion::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
                'poll_question_create',
                $poll_question_obj,
                [
                    new CRUDFormRow('Заголовок', new CRUDFormWidgetInput(PollQuestion::_TITLE)),
                    new CRUDFormRow('Сортировка', new CRUDFormWidgetInput(PollQuestion::_WEIGHT)),
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(PollQuestion::_POLL_ID))
                ]
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(PollQuestion::_ID)),
                new CRUDTableColumn(
                    'Заголовок',
                    new CRUDTableWidgetTextWithLink(
                        PollQuestion::_TITLE,
                        function (PollQuestion $poll_question) {
                            return $this->pathFor(PollRoutes::ROUTE_NAME_ADMIN_POLL_QUESTION_EDIT, ['poll_question_id' => $poll_question->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Проголосовало',
                    new CRUDTableWidgetText(PollQuestion::_VOTES)
                ),
                new CRUDTableColumn(
                    'Сортировка',
                    new CRUDTableWidgetText(PollQuestion::_WEIGHT)
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInvisible(self::FILTER_NAME_POLL_ID, $poll_id),
            ],
            PollQuestion::_WEIGHT . ' DESC'
        );

        $crud_form_table_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_table_response instanceof ResponseInterface) {
            return $crud_form_table_response;
        }

        $content_html .= '<h3>Варианты ответов</h3>';
        $content_html .= $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($poll_obj->getTitle());
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO('Опросы', $this->pathFor(PollRoutes::ROUTE_NAME_ADMIN_POLL_LIST)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);


        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
