<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetReferenceSelect;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\Skif\Content\Rubric;
use WebSK\Skif\Content\Template;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class AdminTemplateListAjaxHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminTemplateListAjaxHandler extends BaseHandler
{
    const string FILTER_TITLE = 'template_title';

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
            Rubric::class,
            null,
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(Template::_ID)),
                new CRUDTableColumn('Название', new CRUDTableWidgetText(Template::_TITLE)),
                new CRUDTableColumn('Обозначение', new CRUDTableWidgetText(Template::_NAME)),
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetReferenceSelect(Template::_TITLE)
                ),
            ],
            [
                new CRUDTableFilterLikeInline(self::FILTER_TITLE, '', Template::_TITLE, 'Название'),
            ],
            Template::_CREATED_AT_TS . ' DESC',
            'template_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $response->getBody()->write($crud_table_obj->html($request));

        return $response;
    }
}
