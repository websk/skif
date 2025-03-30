<?php

namespace WebSK\Skif\SiteMenu\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTimestamp;
use WebSK\Skif\SiteMenu\SiteMenu;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminSiteMenuListHandler
 * @package WebSK\Skif\SiteMenu\RequestHandlers
 */
class AdminSiteMenuListHandler extends BaseHandler
{
    const string FILTER_SITE_MENU_NAME = 'site_menu_name';

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
            SiteMenu::class,
            $this->crud_service->createForm(
                'site_menu_create',
                new SiteMenu(),
                [
                    new CRUDFormRow(
                        'Название меню',
                        new CRUDFormWidgetInput(SiteMenu::_NAME, false, true),
                    ),
                    new CRUDFormRow(
                        'Адрес страницы',
                        new CRUDFormWidgetInput(SiteMenu::_URL),
                        'Заполняется, если нужна ссылка с заголовка меню'
                    ),
                ]
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(SiteMenu::_ID)),
                new CRUDTableColumn(
                    'Название',
                    new CRUDTableWidgetTextWithLink(
                        SiteMenu::_NAME,
                        function (SiteMenu $site_menu) {
                            return $this->urlFor(AdminSiteMenuEditHandler::class, ['site_menu_id' => $site_menu->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Адрес страницы',
                    new CRUDTableWidgetText(
                        SiteMenu::_URL
                    )
                ),
                new CRUDTableColumn(
                    'Создан',
                    new CRUDTableWidgetTimestamp(SiteMenu::_CREATED_AT_TS)
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInline(self::FILTER_SITE_MENU_NAME, 'Название', SiteMenu::_NAME),
            ],
            SiteMenu::_CREATED_AT_TS . ' DESC',
            'site_menu_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Меню сайта');
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}