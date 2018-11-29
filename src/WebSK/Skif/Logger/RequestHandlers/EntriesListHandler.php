<?php

namespace WebSK\Skif\Logger\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\SkifPhpRender;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\LayoutDTO;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLike;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTimestamp;
use WebSK\Skif\Logger\Entry\LoggerEntry;
use WebSK\Skif\Logger\LoggerConstants;
use WebSK\Skif\Logger\LoggerRoutes;

class EntriesListHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            LoggerEntry::class,
            null,
            [
                new CRUDTableColumn(
                    'Объект',
                    new CRUDTableWidgetText('{this->object_full_id}')
                ),
                new CRUDTableColumn(
                    'Дата создания',
                    new CRUDTableWidgetTimestamp('{this->created_at_ts}')
                ),
                new CRUDTableColumn(
                    'Пользователь',
                    new CRUDTableWidgetTextWithLink(
                        '{this->user_full_id}',
                        $this->pathFor(LoggerRoutes::ROUTE_NAME_ADMIN_LOGGER_ENTRY_EDIT, ['entry_id' => '{this->id}'])
                    )
                )
            ],
            [
                new CRUDTableFilterLike('38947yt7ywssserkit22uy', 'Object Fullid', LoggerEntry::_OBJECT_FULLID),
            ],
            'created_at_ts desc',
            '8273649529',
            CRUDTable::FILTERS_POSITION_TOP
        );

        $crud_table_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_table_response instanceof Response) {
            return $crud_table_response;
        }

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Журналы');
        $layout_dto->setContentHtml($crud_table_obj->html($request));
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', LoggerConstants::ADMIN_ROOT_PATH),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return SkifPhpRender::renderLayout($response, $layout_dto);
    }
}
