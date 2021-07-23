<?php

namespace WebSK\Skif\SiteMenu\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetReferenceSelect;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\Skif\SiteMenu\SiteMenuItem;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class AdminSiteMenuItemListAjaxHandler
 * @package WebSK\Skif\SiteMenu\RequestHandlers
 */
class AdminSiteMenuItemListAjaxHandler extends BaseHandler
{
    const FILTER_SITE_MENU_ITEM_NAME = 'site_menu_item_name';

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            SiteMenuItem::class,
            null,
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(SiteMenuItem::_ID)),
                new CRUDTableColumn('Название', new CRUDTableWidgetText(SiteMenuItem::_NAME)),
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetReferenceSelect(SiteMenuItem::_NAME)
                ),
            ],
            [
                new CRUDTableFilterLikeInline(
                    self::FILTER_SITE_MENU_ITEM_NAME,
                    '', SiteMenuItem::_NAME,
                    'Название'
                ),
            ],
            SiteMenuItem::_CREATED_AT_TS . ' DESC',
            'site_menu_item_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        return $response->write($crud_table_obj->html($request));
    }
}