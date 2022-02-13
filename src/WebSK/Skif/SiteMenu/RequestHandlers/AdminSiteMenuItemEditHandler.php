<?php

namespace WebSK\Skif\SiteMenu\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDServiceProvider;
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
use WebSK\Skif\SiteMenu\SiteMenuItemService;
use WebSK\Skif\SiteMenu\SiteMenuService;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\HTTP;
use WebSK\Views\LayoutDTO;
use WebSK\Views\NavTabItemDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminSiteMenuItemEditHandler
 * @package WebSK\Skif\SiteMenu\RequestHandlers
 */
class AdminSiteMenuItemEditHandler extends BaseHandler
{
    use AdminSiteMenuBreadcrumbsTrait;

    const FILTER_NAME_MENU_ID = 'menu_id';
    const FILTER_NAME_SITE_MENU_ITEM_PARENT_ID = 'parent_id';

    /** @var SiteMenuService */
    protected SiteMenuService $site_menu_service;

    /** @var SiteMenuItemService */
    protected SiteMenuItemService $site_menu_item_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $site_menu_item_id
     * @return ResponseInterface
     * @throws \ReflectionException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $site_menu_item_id): ResponseInterface
    {
        $this->site_menu_service = $this->container->get(SiteMenuService::class);

        $this->site_menu_item_service = $this->container->get(SiteMenuItemService::class);

        $site_menu_item_obj = $this->site_menu_item_service->getById($site_menu_item_id, false);

        if (!$site_menu_item_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $crud_form = CRUDServiceProvider::getCrud($this->container)->createForm(
            'site_menu_item_edit',
            $site_menu_item_obj,
            [
                new CRUDFormRow('Название', new CRUDFormWidgetInput(SiteMenuItem::_NAME, false, true)),
                new CRUDFormRow(
                    'Меню',
                    new CRUDFormWidgetReferenceAjax(
                        SiteMenuItem::_MENU_ID,
                        SiteMenu::class,
                        SiteMenu::_NAME,
                        $this->pathFor(AdminSiteMenuListAjaxHandler::class),
                        $this->pathFor(AdminSiteMenuEditHandler::class, ['site_menu_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER])
                    )
                ),
                new CRUDFormRow(
                    'Родительский пункт',
                    new CRUDFormWidgetReferenceAjax(
                        SiteMenuItem::_PARENT_ID,
                        SiteMenuItem::class,
                        SiteMenuItem::_NAME,
                        $this->pathFor(AdminSiteMenuItemListAjaxHandler::class),
                        $this->pathFor(AdminSiteMenuItemEditHandler::class, ['site_menu_item_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER])
                    )
                ),
                new CRUDFormRow(
                    'Контент',
                    new CRUDFormWidgetReferenceAjax(
                        SiteMenuItem::_CONTENT_ID,
                        Content::class,
                        Content::_TITLE,
                        $this->pathFor(AdminContentListAjaxHandler::class, ['content_type' => ContentType::CONTENT_TYPE_PAGE]),
                        $this->pathFor(
                            AdminContentEditHandler::class,
                            [
                                'content_type' => ContentType::CONTENT_TYPE_PAGE,
                                'content_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER]
                        )
                    )
                ),
                new CRUDFormRow('Адрес страницы', new CRUDFormWidgetInput(SiteMenuItem::_URL)),
                new CRUDFormRow('Опубликовано', new CRUDFormWidgetRadios(SiteMenuItem::_IS_PUBLISHED, [false => 'Нет', true => 'Да'])),
            ]
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();

        $children_site_menu_item_obj = new SiteMenuItem();
        $children_site_menu_item_obj->setMenuId($site_menu_item_obj->getMenuId());
        $children_site_menu_item_obj->setParentId($site_menu_item_id);

        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            SiteMenuItem::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
                'site_menu_item_create',
                $children_site_menu_item_obj,
                [
                    new CRUDFormRow('Название', new CRUDFormWidgetInput(SiteMenuItem::_NAME, false, true)),
                    new CRUDFormRow(
                        'Контент',
                        new CRUDFormWidgetReferenceAjax(
                            SiteMenuItem::_CONTENT_ID,
                            Content::class,
                            Content::_TITLE,
                            $this->pathFor(AdminContentListAjaxHandler::class, ['content_type' => ContentType::CONTENT_TYPE_PAGE]),
                            $this->pathFor(
                                AdminContentEditHandler::class,
                                [
                                    'content_type' => ContentType::CONTENT_TYPE_PAGE,
                                    'content_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER]
                            )
                        )
                    ),
                    new CRUDFormRow('Адрес страницы', new CRUDFormWidgetInput(SiteMenuItem::_URL)),
                    new CRUDFormRow('Опубликовано', new CRUDFormWidgetRadios(SiteMenuItem::_IS_PUBLISHED, [false => 'Нет', true => 'Да'])),
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(SiteMenuItem::_MENU_ID)),
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(SiteMenuItem::_PARENT_ID))
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
                            return $this->pathFor(AdminSiteMenuItemEditHandler::class, ['site_menu_item_id' => $site_menu_item->getId()]);
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
                new CRUDTableFilterEqualInvisible(self::FILTER_NAME_MENU_ID, $site_menu_item_obj->getMenuId()),
                new CRUDTableFilterEqualInvisible(self::FILTER_NAME_SITE_MENU_ITEM_PARENT_ID, $site_menu_item_id),
            ],
            SiteMenuItem::_WEIGHT
        );

        $crud_form_table_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_table_response instanceof ResponseInterface) {
            return $crud_form_table_response;
        }

        $content_html .= '<h3>Вложенные пункты меню</h3>';
        $content_html .= $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($site_menu_item_obj->getName());
        $layout_dto->setContentHtml($content_html);
        $layout_dto->setBreadcrumbsDtoArr($this->getBreadcrumbsDTOArr($site_menu_item_obj->getMenuId(), $site_menu_item_id));

        $layout_dto->setNavTabsDtoArr(
            [
                new NavTabItemDTO(
                    'Редактирование',
                    $this->pathFor(
                        self::class,
                        ['site_menu_item_id' => $site_menu_item_id]
                    )
                ),
                new NavTabItemDTO('Журнал', LoggerRender::getLoggerLinkForEntityObj($site_menu_item_obj), '_blank'),
            ]
        );

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}