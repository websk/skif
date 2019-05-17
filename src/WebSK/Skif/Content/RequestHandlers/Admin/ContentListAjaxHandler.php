<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInvisible;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetReferenceSelect;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\Skif\Content\Content;
use WebSK\Skif\Content\ContentType;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class ContentListAjaxHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class ContentListAjaxHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param string $content_type
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response, string $content_type)
    {
        $content_type_obj = ContentType::factoryByFieldsArr(['type' => $content_type]);

        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            Content::class,
            null,
            [
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetReferenceSelect(Content::_TITLE)
                ),
                new CRUDTableColumn(
                    'Название',
                    new CRUDTableWidgetText('{this->' . Content::_TITLE . '}')
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
