<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormInvisibleRow;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInvisible;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualOptionsInline;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTimestamp;
use WebSK\Skif\Content\Content;
use WebSK\Skif\Content\ContentTypeService;
use WebSK\Skif\Content\Rubric;
use WebSK\Skif\Content\RubricService;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminContentListHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminContentListHandler extends BaseHandler
{
    const string FILTER_TITLE = 'content_title';
    const string FILTER_RUBRICS = 'content_rubric_id';
    const string FILTER_CONTENT_TYPE_ID = 'content_type_id';

    /** @Inject */
    protected CRUD $crud_service;

    /** @Inject */
    protected ContentTypeService $content_type_service;

    /** @Inject */
    protected RubricService $rubric_service;

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

        $new_content_obj = new Content();
        $new_content_obj->setContentTypeId($content_type_obj->getId());

        $rubric_ids_arr = $this->rubric_service->getIdsArrByContentTypeId($content_type_obj->getId());
        $rubric_for_options_arr = [];
        foreach ($rubric_ids_arr as $rubric_id) {
            $rubric_obj = $this->rubric_service->getById($rubric_id);
            $rubric_for_options_arr[$rubric_id] = $rubric_obj->getName();
        }

        $crud_table_obj = $this->crud_service->createTable(
            Content::class,
            $this->crud_service->createForm(
                'content_create',
                $new_content_obj,
                [
                    new CRUDFormRow('Заголовок', new CRUDFormWidgetInput(Content::_TITLE)),
                    new CRUDFormRow(
                        'Главная рубрика',
                        new CRUDFormWidgetReferenceAjax(
                            Content::_MAIN_RUBRIC_ID,
                            Rubric::class,
                            Rubric::_NAME,
                            $this->urlFor(AdminRubricListAjaxHandler::class, ['content_type' => $content_type]),
                            $this->urlFor(
                                AdminRubricEditHandler::class,
                                ['content_type' => $content_type, 'rubric_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER]
                            )
                        )
                    ),
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(Content::_CONTENT_TYPE_ID)),
                ]
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(Content::_ID)),
                new CRUDTableColumn(
                    'Заголовок',
                    new CRUDTableWidgetTextWithLink(
                        Content::_TITLE,
                        function (Content $content) use ($content_type) {
                            return $this->urlFor(AdminContentEditHandler::class, ['content_type' => $content_type, 'content_id' => $content->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Создан',
                    new CRUDTableWidgetTimestamp(Content::_CREATED_AT_TS)
                ),
                new CRUDTableColumn(
                    'Ссылка',
                    new CRUDTableWidgetTextWithLink(
                        Content::_URL,
                        function (Content $content) {
                            return $content->getUrl();
                        },
                        '',
                        '_blank'
                    )
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInvisible(self::FILTER_CONTENT_TYPE_ID, $content_type_obj->getId()),
                new CRUDTableFilterLikeInline(self::FILTER_TITLE, 'Заголовок', Content::_TITLE),
                new CRUDTableFilterEqualOptionsInline(
                    self::FILTER_RUBRICS,
                    'Рубрика',
                    Content::_MAIN_RUBRIC_ID,
                    $rubric_for_options_arr,
                    false,
                    false,
                    false
                ),
            ],
            Content::_CREATED_AT_TS . ' DESC',
            'content_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = '<p><a href="' . $this->urlFor(AdminRubricListHandler::class, ['content_type' => $content_type]) . '">
            Редактировать рубрики
        </a></p>';

        $content_html .= $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($content_type_obj->getName());
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
