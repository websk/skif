<?php

namespace WebSK\Skif\Poll\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetDate;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetRadios;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetHtml;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetOptions;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTimestamp;
use WebSK\Skif\Poll\Poll;
use WebSK\Skif\Poll\RequestHandlers\PollViewHandler;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminPollListHandler
 * @package WebSK\Skif\Poll\RequestHandlers\Admin
 */
class AdminPollListHandler extends BaseHandler
{
    const FILTER_TITLE = 'poll_title';

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            Poll::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
                'poll_create',
                new Poll(),
                [
                    new CRUDFormRow('Заголовок', new CRUDFormWidgetInput(Poll::_TITLE)),
                    new CRUDFormRow('По умолчанию', new CRUDFormWidgetRadios(Poll::_IS_DEFAULT, [false => 'Нет', true => 'Да'])),
                    new CRUDFormRow('Опубликовано', new CRUDFormWidgetRadios(Poll::_IS_PUBLISHED, [false => 'Нет', true => 'Да'])),
                    new CRUDFormRow('Показывать с', new CRUDFormWidgetDate(Poll::_PUBLISHED_AT)),
                    new CRUDFormRow('Показывать по', new CRUDFormWidgetDate(Poll::_UNPUBLISHED_AT)),
                ]
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(Poll::_ID)),
                new CRUDTableColumn(
                    'Заголовок',
                    new CRUDTableWidgetTextWithLink(
                        Poll::_TITLE,
                        function (Poll $poll) {
                            return $this->pathFor(AdminPollEditHandler::class, ['poll_id' => $poll->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'По умолчанию',
                    new CRUDTableWidgetOptions(
                        Poll::_IS_DEFAULT,
                        [false => 'Нет', true => 'Да']
                    )
                ),
                new CRUDTableColumn(
                    'Опубликовано',
                    new CRUDTableWidgetOptions(
                        Poll::_IS_PUBLISHED,
                        [false => 'Нет', true => 'Да']
                    )
                ),
                new CRUDTableColumn(
                    'Ссылка',
                    new CRUDTableWidgetHtml(
                        function (Poll $poll) {
                            $poll_url = $this->pathFor(PollViewHandler::class, ['poll_id' => $poll->getId()]);
                            return '<a href="' . $poll_url . '" target="_blank">' . $poll_url . '</a>';
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Создан',
                    new CRUDTableWidgetTimestamp(Poll::_CREATED_AT_TS)
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInline(self::FILTER_TITLE, 'Заголовок', Poll::_TITLE),
            ],
            Poll::_CREATED_AT_TS . ' DESC'
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Опросы');
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
