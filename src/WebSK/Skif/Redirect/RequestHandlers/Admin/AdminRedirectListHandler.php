<?php

namespace WebSK\Skif\Redirect\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetOptions;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTimestamp;
use WebSK\Skif\Redirect\Redirect;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminRedirectListHandler
 * @package WebSK\Skif\Redirect\RequestHandlers\Admin
 */
class AdminRedirectListHandler extends BaseHandler
{
    const FILTER_SRC = 'redirect_src';
    const FILTER_DST = 'redirect_dst';

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            Redirect::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
                'redirect_create',
                new Redirect(),
                [
                    new CRUDFormRow('Исходный URL', new CRUDFormWidgetInput(Redirect::_SRC)),
                    new CRUDFormRow('Назначение', new CRUDFormWidgetInput(Redirect::_DST)),
                    new CRUDFormRow('HTTP код', new CRUDFormWidgetInput(Redirect::_CODE)),
                    new CRUDFormRow('Вид', new CRUDFormWidgetOptions(Redirect::_KIND, Redirect::REDIRECT_KINDS_ARR))
                ]
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(Redirect::_ID)),
                new CRUDTableColumn(
                    'Исходный URL',
                    new CRUDTableWidgetTextWithLink(
                        Redirect::_SRC,
                        function (Redirect $redirect) {
                            return $this->pathFor(AdminRedirectEditHandler::class, ['redirect_id' => $redirect->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Назначение',
                    new CRUDTableWidgetText(
                        Redirect::_DST
                    )
                ),
                new CRUDTableColumn(
                    'Создан',
                    new CRUDTableWidgetTimestamp(Redirect::_CREATED_AT_TS)
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterLikeInline(self::FILTER_SRC, 'Исходный URL', Redirect::_SRC),
                new CRUDTableFilterLikeInline(self::FILTER_DST, 'Назначение', Redirect::_DST),
            ],
            Redirect::_CREATED_AT_TS . ' DESC',
            'redirect_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Редиректы');
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
