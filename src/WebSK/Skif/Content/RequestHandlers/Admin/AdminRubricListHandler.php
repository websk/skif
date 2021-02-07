<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\CRUD\CRUDServiceProvider;
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
use WebSK\Skif\Content\ContentRoutes;
use WebSK\Skif\Content\ContentServiceProvider;
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
    const FILTER_CONTENT_TYPE_ID = 'content_type_id';
    const FILTER_NAME = 'content_type_name';

    /**
     * @param Request $request
     * @param Response $response
     * @param string $content_type
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response, string $content_type)
    {
        $content_type_obj = ContentServiceProvider::getContentTypeService($this->container)
            ->getByType($content_type);

        if (!$content_type_obj) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        $new_rubric_obj = new Rubric();
        $new_rubric_obj->setContentTypeId($content_type_obj->getId());

        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            Rubric::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
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
                            return $this->pathFor(ContentRoutes::ROUTE_NAME_ADMIN_RUBRIC_EDIT, ['content_type' => $content_type, 'rubric_id' => $rubric->getId()]);
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
        if ($crud_form_response instanceof Response) {
            return $crud_form_response;
        }

        $content_html = $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Рубрики');
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO($content_type_obj->getName(), '/admin/content/' . $content_type),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
