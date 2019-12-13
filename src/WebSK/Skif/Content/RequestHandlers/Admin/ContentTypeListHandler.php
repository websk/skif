<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTimestamp;
use WebSK\Skif\Content\ContentRoutes;
use WebSK\Skif\Content\ContentType;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class ContentTypeListHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class ContentTypeListHandler extends BaseHandler
{
    const FILTER_NAME = 'content_type_name';

    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            ContentType::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
                'form_create',
                new ContentType(),
                [
                    new CRUDFormRow('Название', new CRUDFormWidgetInput(ContentType::_NAME)),
                    new CRUDFormRow('Тип', new CRUDFormWidgetInput(ContentType::_TYPE)),
                    new CRUDFormRow('URL', new CRUDFormWidgetInput(ContentType::_URL)),
                    new CRUDFormRow(
                        'Шаблон',
                        new CRUDFormWidgetInput(ContentType::_TEMPLATE_ID)
                    ),
                ]
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(ContentType::_ID)),
                new CRUDTableColumn(
                    'Название',
                    new CRUDTableWidgetTextWithLink(
                        ContentType::_NAME,
                        function (ContentType $content_type) {
                            return $this->pathFor(ContentRoutes::ROUTE_NAME_ADMIN_CONTENT_TYPE_EDIT, ['content_type_id' => $content_type->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Тип',
                    new CRUDTableWidgetText(
                        ContentType::_TYPE
                    )
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInline(self::FILTER_NAME, 'Название', ContentType::_NAME),
            ],
            ContentType::_CREATED_AT_TS . ' DESC'
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof Response) {
            return $crud_form_response;
        }

        $content_html = $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Типы контента');
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
