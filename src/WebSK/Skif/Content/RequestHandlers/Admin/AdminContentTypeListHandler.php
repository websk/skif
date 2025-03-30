<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\Skif\Content\ContentType;
use WebSK\Skif\Content\Template;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminContentTypeListHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminContentTypeListHandler extends BaseHandler
{
    const string FILTER_NAME = 'content_type_name';

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
            ContentType::class,
            $this->crud_service->createForm(
                'content_type_create',
                new ContentType(),
                [
                    new CRUDFormRow('Название', new CRUDFormWidgetInput(ContentType::_NAME)),
                    new CRUDFormRow('Тип', new CRUDFormWidgetInput(ContentType::_TYPE)),
                    new CRUDFormRow('URL', new CRUDFormWidgetInput(ContentType::_URL)),
                    new CRUDFormRow(
                        'Шаблон',
                        new CRUDFormWidgetReferenceAjax(
                            ContentType::_TEMPLATE_ID,
                            Template::class,
                            Template::_TITLE,
                            $this->urlFor(AdminTemplateListAjaxHandler::class),
                            $this->urlFor(
                                AdminTemplateEditHandler::class,
                                ['template_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER]
                            )
                        )
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
                            return $this->urlFor(AdminContentTypeEditHandler::class, ['content_type_id' => $content_type->getId()]);
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
                new CRUDTableFilterLikeInline(self::FILTER_NAME, 'Название', ContentType::_NAME),
            ],
            ContentType::_CREATED_AT_TS . ' DESC',
            'content_type_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
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
