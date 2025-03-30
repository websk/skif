<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInvisible;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetReferenceSelect;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\Skif\Content\Content;
use WebSK\Skif\Content\ContentTypeService;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class AdminContentListAjaxHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminContentListAjaxHandler extends BaseHandler
{
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

        $crud_table_obj = $this->crud_service->createTable(
            Content::class,
            null,
            [
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetReferenceSelect(Content::_TITLE)
                ),
                new CRUDTableColumn(
                    'Название',
                    new CRUDTableWidgetText(Content::_TITLE)
                )
            ],
            [
                new CRUDTableFilterLikeInline('content_name', '', Content::_TITLE, 'Название'),
                new CRUDTableFilterEqualInvisible('content_type_id', $content_type_obj->getId()),
            ],
            Content::_TITLE,
            'content_list_rand324932',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        return $response->write($crud_table_obj->html($request));
    }
}
