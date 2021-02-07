<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetReferenceSelect;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\Skif\Content\Template;
use WebSK\Skif\Form\Form;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class AdminTemplateListAjaxHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminTemplateListAjaxHandler extends BaseHandler
{
    const FILTER_TITLE = 'template_title';

    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface|Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            Form::class,
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
            Form::_CREATED_AT_TS . ' DESC',
            'template_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        return $response->write($crud_table_obj->html($request));
    }
}
