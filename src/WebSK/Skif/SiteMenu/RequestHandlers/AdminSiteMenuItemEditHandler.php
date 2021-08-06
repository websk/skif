<?php

namespace WebSK\Skif\SiteMenu\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetRadios;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
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

        $this->site_menu_service = $this->container->get(SiteMenuService::class);

        $content_html = $crud_form->html();
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