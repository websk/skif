<?php

namespace WebSK\Skif\Form\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetReferenceSelect;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\Skif\Form\Form;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class AdminFormListAjaxHandler
 * @package WebSK\Skif\Form\RequestHandlers\Admin
 */
class AdminFormListAjaxHandler extends BaseHandler
{
    const FILTER_TITLE = 'title';

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            Form::class,
            null,
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(Form::_ID)),
                new CRUDTableColumn('Название', new CRUDTableWidgetText(Form::_TITLE)),
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetReferenceSelect(Form::_TITLE)
                ),
            ],
            [
                new CRUDTableFilterLikeInline(self::FILTER_TITLE, '', Form::_TITLE, 'Название'),
            ],
            Form::_CREATED_AT_TS . ' DESC',
            'form_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        return $response->write($crud_table_obj->html($request));
    }
}
