<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\Skif\Content\ContentRoutes;
use WebSK\Skif\Content\Template;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminTemplateListHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminTemplateListHandler extends BaseHandler
{
    const FILTER_TITLE = 'template_title';

    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            Template::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
                'form_create',
                new Template(),
                [
                    new CRUDFormRow('Название', new CRUDFormWidgetInput(Template::_TITLE)),
                    new CRUDFormRow('Обозначение', new CRUDFormWidgetInput(Template::_NAME)),
                    new CRUDFormRow('Файл CSS', new CRUDFormWidgetInput(Template::_CSS)),
                    new CRUDFormRow('Файл шаблона', new CRUDFormWidgetInput(Template::_LAYOUT_TEMPLATE_FILE)),
                ]
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(Template::_ID)),
                new CRUDTableColumn(
                    'Название',
                    new CRUDTableWidgetTextWithLink(
                        Template::_TITLE,
                        function (Template $template) {
                            return $this->pathFor(ContentRoutes::ROUTE_NAME_ADMIN_TEMPLATE_EDIT, ['template_id' => $template->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Обозначение',
                    new CRUDTableWidgetText(
                        Template::_NAME
                    )
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterLikeInline(self::FILTER_TITLE, 'Название', Template::_TITLE),
            ],
            Template::_CREATED_AT_TS . ' DESC',
            'template_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof Response) {
            return $crud_form_response;
        }

        $content_html = $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Темы');
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }

}
