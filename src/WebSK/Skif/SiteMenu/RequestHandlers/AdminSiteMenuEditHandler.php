<?php

namespace WebSK\Skif\SiteMenu\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormInvisibleRow;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetRadios;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInvisible;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetWeight;
use WebSK\Logger\LoggerRender;
use WebSK\Skif\Content\Content;
use WebSK\Skif\Content\ContentType;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentEditHandler;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentListAjaxHandler;
use WebSK\Skif\SiteMenu\SiteMenu;
use WebSK\Skif\SiteMenu\SiteMenuItem;
use WebSK\Skif\SiteMenu\SiteMenuService;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\LayoutDTO;
use WebSK\Views\NavTabItemDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminSiteMenuEditHandler
 * @package WebSK\Skif\SiteMenu\RequestHandlers
 */
class AdminSiteMenuEditHandler extends BaseHandler
{
    use AdminSiteMenuBreadcrumbsTrait;

    const string FILTER_NAME_MENU_ID = 'menu_id';
    const string FILTER_NAME_SITE_MENU_ITEM_PARENT_ID = 'parent_id';

    /** @Inject */
    protected SiteMenuService $site_menu_service;

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $site_menu_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $site_menu_id): ResponseInterface
    {
        $site_menu_obj = $this->site_menu_service->getById($site_menu_id, false);

        if (!$site_menu_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $crud_form = $this->crud_service->createForm(
            'site_menu_edit',
            $site_menu_obj,
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
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();

        $site_menu_item_obj = new SiteMenuItem();
        $site_menu_item_obj->setMenuId($site_menu_id);

        $crud_table_obj = $this->crud_service->createTable(
            SiteMenuItem::class,
            $this->crud_service->createForm(
                'site_menu_item_create',
                $site_menu_item_obj,
                [
                    new CRUDFormRow('Название', new CRUDFormWidgetInput(SiteMenuItem::_NAME, false, true)),
                    new CRUDFormRow(
                        'Контент',
                        new CRUDFormWidgetReferenceAjax(
                            SiteMenuItem::_CONTENT_ID,
                            Content::class,
                            Content::_TITLE,
                            $this->urlFor(AdminContentListAjaxHandler::class, ['content_type' => ContentType::CONTENT_TYPE_PAGE]),
                            $this->urlFor(
                                AdminContentEditHandler::class,
                                [
                                    'content_type' => ContentType::CONTENT_TYPE_PAGE,
                                    'content_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER]
                            )
                        )
                    ),
                    new CRUDFormRow('Адрес страницы', new CRUDFormWidgetInput(SiteMenuItem::_URL)),
                    new CRUDFormRow('Опубликовано', new CRUDFormWidgetRadios(SiteMenuItem::_IS_PUBLISHED, [false => 'Нет', true => 'Да'])),
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(SiteMenuItem::_MENU_ID))
                ]
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(SiteMenuItem::_ID)),
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetWeight([SiteMenuItem::_MENU_ID => SiteMenuItem::_MENU_ID])
                ),
                new CRUDTableColumn(
                    'Вес',
                    new CRUDTableWidgetText(
                        SiteMenuItem::_WEIGHT
                    )
                ),
                new CRUDTableColumn(
                    'Название',
                    new CRUDTableWidgetTextWithLink(
                        SiteMenuItem::_NAME,
                        function (SiteMenuItem $site_menu_item) {
                            return $this->urlFor(AdminSiteMenuItemEditHandler::class, ['site_menu_item_id' => $site_menu_item->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Адрес страницы',
                    new CRUDTableWidgetText(SiteMenuItem::_URL)
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInvisible(self::FILTER_NAME_MENU_ID, $site_menu_id),
                new CRUDTableFilterEqualInvisible(self::FILTER_NAME_SITE_MENU_ITEM_PARENT_ID, null),
            ],
            SiteMenuItem::_WEIGHT
        );

        $crud_form_table_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_table_response instanceof ResponseInterface) {
            return $crud_form_table_response;
        }

        $content_html .= '<h3>Пункты меню</h3>';
        $content_html .= $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($site_menu_obj->getName());
        $layout_dto->setContentHtml($content_html);
        $layout_dto->setBreadcrumbsDtoArr($this->getBreadcrumbsDTOArr($site_menu_item_obj->getMenuId()));

        $layout_dto->setNavTabsDtoArr(
            [
                new NavTabItemDTO(
                    'Редактирование',
                    $this->urlFor(
                        self::class,
                        ['site_menu_id' => $site_menu_id]
                    )
                ),
                new NavTabItemDTO('Журнал', LoggerRender::getLoggerLinkForEntityObj($site_menu_obj), '_blank'),
            ]
        );

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}