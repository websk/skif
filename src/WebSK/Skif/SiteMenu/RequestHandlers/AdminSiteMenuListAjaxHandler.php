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
use WebSK\Skif\SiteMenu\SiteMenu;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class AdminSiteMenuListAjaxHandler
 * @package WebSK\Skif\SiteMenu\RequestHandlers
 */
class AdminSiteMenuListAjaxHandler extends BaseHandler
{
    const FILTER_SITE_MENU_NAME = 'site_menu_name';

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            SiteMenu::class,
            null,
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(SiteMenu::_ID)),
                new CRUDTableColumn('Название', new CRUDTableWidgetText(SiteMenu::_NAME)),
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetReferenceSelect(SiteMenu::_NAME)
                ),
            ],
            [
                new CRUDTableFilterLikeInline(
                    self::FILTER_SITE_MENU_NAME,
                    '', SiteMenu::_NAME,
                    'Название'
                ),
            ],
            SiteMenu::_CREATED_AT_TS . ' DESC',
            'site_menu_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        return $response->write($crud_table_obj->html($request));
    }
}