<?php

namespace WebSK\Skif\Poll\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetReferenceSelect;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\Skif\Poll\Poll;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class AdminPollListAjaxHandler
 * @package WebSK\Skif\Poll\RequestHandlers\Admin
 */
class AdminPollListAjaxHandler extends BaseHandler
{
    const string FILTER_TITLE = 'title';

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $crud_table_obj = $this->crud_service->createTable(
            Poll::class,
            null,
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(Poll::_ID)),
                new CRUDTableColumn('Заголовок', new CRUDTableWidgetText(Poll::_TITLE)),
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetReferenceSelect(Poll::_TITLE)
                ),
            ],
            [
                new CRUDTableFilterLikeInline(self::FILTER_TITLE, '', Poll::_TITLE, 'Заголовок'),
            ],
            Poll::_CREATED_AT_TS . ' DESC',
            'poll_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $response->getBody()->write($crud_table_obj->html($request));

        return $response;
    }
}
