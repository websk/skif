<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualOptionsInline;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetHtml;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetHtmlWithLink;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTimestamp;
use WebSK\Skif\Blocks\Block;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\PageRegion;
use WebSK\Skif\Blocks\PageRegionService;
use WebSK\Skif\Content\TemplateService;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

class BlockListHandler extends BaseHandler
{
    use CurrentTemplateIdTrait;

    const string FILTER_TITLE = 'block_title';

    const string FILTER_BODY = 'block_body';

    const string FILTER_REGION = 'block_region';

    const string FILTER_TEMPLATE = 'block_template';

    /** @Inject */
    protected BlockService $block_service;

    /** @Inject */
    protected PageRegionService $page_region_service;

    /** @Inject */
    protected TemplateService $template_service;

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $page_region_ids_arr = $this->page_region_service->getIdsArrByTemplateId($this->getCurrentTemplateId());
        $page_region_for_options_arr = [];
        foreach ($page_region_ids_arr as $page_region_id) {
            $page_region_obj = $this->page_region_service->getById($page_region_id);
            $page_region_for_options_arr[$page_region_id] = $page_region_obj->getTitle();
        }

        $templates_ids_arr = $this->template_service->getAllIdsArrByIdAsc();
        $templates_for_options_arr = [];
        foreach ($templates_ids_arr as $template_id) {
            $template_obj = $this->template_service->getById($template_id);
            $templates_for_options_arr[$template_id] = $template_obj->getTitle();
        }

        $crud_table_obj = $this->crud_service->createTable(
            Block::class,
            $this->crud_service->createForm(
                'block_create',
                new Block(),
                [
                    new CRUDFormRow('Название', new CRUDFormWidgetInput(Block::_TITLE))
                ]
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(Block::_ID)),
                new CRUDTableColumn(
                    'Название',
                    new CRUDTableWidgetTextWithLink(
                        Block::_TITLE,
                        function (Block $block) {
                            return $this->urlFor(BlockEditorContentHandler::class, ['block_id' => $block->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Регион',
                    new CRUDTableWidgetText(
                        function (Block $block) {
                            $page_region_obj = $this->page_region_service->getById($block->getPageRegionId());
                            return $page_region_obj->getTitle();
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Создан',
                    new CRUDTableWidgetTimestamp(Block::_CREATED_AT_TS)
                ),
                new CRUDTableColumn(
                    'Выключение',
                    new CRUDTableWidgetHtml(
                        function (Block $block) {
                            if ($block->getPageRegionId() == PageRegion::BLOCK_REGION_NONE) {
                                return  '';
                            }

                            return '<a href="' . $this->urlFor(BlockDisableHandler::class, ['block_id' => $block->getId()]) . '" title="Отключить" class="btn btn-default btn-sm">'
                            . '<span class="fa fa-power-off fa-lg text-muted fa-fw"></span>'
                            . '</a>';
                        }
                    )
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterLikeInline(self::FILTER_TITLE, 'Название', Block::_TITLE),
                new CRUDTableFilterLikeInline(self::FILTER_BODY, 'Содержимое', Block::_BODY),
                new CRUDTableFilterEqualOptionsInline(
                    self::FILTER_TEMPLATE,
                    'Шаблон',
                    Block::_TEMPLATE_ID,
                    $templates_for_options_arr,
                    false,
                    false,
                    false
                ),
                new CRUDTableFilterEqualOptionsInline(
                    self::FILTER_REGION,
                    'Регион',
                    Block::_PAGE_REGION_ID,
                    $page_region_for_options_arr,
                    false,
                    false,
                    false
                ),
            ],
            Block::_CREATED_AT_TS . ' DESC',
            'block_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Блоки');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}