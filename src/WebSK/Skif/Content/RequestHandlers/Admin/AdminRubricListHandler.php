<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormInvisibleRow;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInvisible;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\Skif\Content\ContentTypeService;
use WebSK\Skif\Content\Rubric;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminRubricListHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminRubricListHandler extends BaseHandler
{
    const string FILTER_CONTENT_TYPE_ID = 'content_type_id';
    const string FILTER_NAME = 'content_type_name';

    /** @Inject */
    protected ContentTypeService $content_type_service;

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $content_type
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $content_type): ResponseInterface
    {
        $content_type_obj = $this->content_type_service->getByType($content_type);
        if (!$content_type_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $new_rubric_obj = new Rubric();
        $new_rubric_obj->setContentTypeId($content_type_obj->getId());

        $crud_table_obj = $this->crud_service->createTable(
            Rubric::class,
            $this->crud_service->createForm(
                'rubric_create',
                $new_rubric_obj,
                [
                    new CRUDFormRow('Название', new CRUDFormWidgetInput(Rubric::_NAME)),
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(Rubric::_CONTENT_TYPE_ID)),
                ]
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(Rubric::_ID)),
                new CRUDTableColumn(
                    'Название',
                    new CRUDTableWidgetTextWithLink(
                        Rubric::_NAME,
                        function (Rubric $rubric) use ($content_type) {
                            return $this->urlFor(AdminRubricEditHandler::class, ['content_type' => $content_type, 'rubric_id' => $rubric->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Ссылка',
                    new CRUDTableWidgetText(
                        Rubric::_URL
                    )
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInvisible(self::FILTER_CONTENT_TYPE_ID, $content_type_obj->getId()),
                new CRUDTableFilterLikeInline(self::FILTER_NAME, 'Название', Rubric::_NAME),
            ],
            Rubric::_CREATED_AT_TS . ' DESC',
            'rubric_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Рубрики');
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO($content_type_obj->getName(), $this->urlFor(AdminContentTypeListHandler::class, ['content_type' => $content_type])),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
