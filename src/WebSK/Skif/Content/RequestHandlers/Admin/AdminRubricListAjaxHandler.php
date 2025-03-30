<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInvisible;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetReferenceSelect;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\Skif\Content\ContentTypeService;
use WebSK\Skif\Content\Rubric;
use WebSK\Slim\RequestHandlers\BaseHandler;

class AdminRubricListAjaxHandler extends BaseHandler
{

    const string FILTER_CONTENT_TYPE_ID = 'content_type_id';
    const string FILTER_NAME = 'name';

    /** @Inject */
    protected ContentTypeService $content_type_service;

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $content_type
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $content_type): ResponseInterface
    {
        $content_type_obj = $this->content_type_service->getByType($content_type);
        if (!$content_type_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $crud_table_obj = $this->crud_service->createTable(
            Rubric::class,
            null,
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(Rubric::_ID)),
                new CRUDTableColumn('Название', new CRUDTableWidgetText(Rubric::_NAME)),
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetReferenceSelect(Rubric::_NAME)
                ),
            ],
            [
                new CRUDTableFilterEqualInvisible(self::FILTER_CONTENT_TYPE_ID, $content_type_obj->getId()),
                new CRUDTableFilterLikeInline(self::FILTER_NAME, '', Rubric::_NAME, 'Название'),
            ],
            Rubric::_CREATED_AT_TS . ' DESC',
            'rubric_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        return $response->write($crud_table_obj->html($request));
    }
}