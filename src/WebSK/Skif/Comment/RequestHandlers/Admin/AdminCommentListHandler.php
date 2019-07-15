<?php

namespace WebSK\Skif\Comment\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Config\ConfWrapper;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTextarea;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInvisible;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTimestamp;
use WebSK\Skif\Comment\Comment;
use WebSK\Skif\Comment\CommentRoutes;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class CommentListHandler
 * @package WebSK\Skif\Comment\RequestHandlers\Admin
 */
class AdminCommentListHandler extends BaseHandler
{
    const FILTER_NAME_PARENT_ID = 'parent_id';

    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            Comment::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
                'comment_create_rand324324',
                new Comment(),
                [
                    new CRUDFormRow('Комментарий', new CRUDFormWidgetTextarea(Comment::_COMMENT)),
                    new CRUDFormRow('Адрес страницы', new CRUDFormWidgetInput(Comment::_URL))
                ]
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText('{this->' . Comment::_ID . '}')),
                new CRUDTableColumn(
                    'Комментарий',
                    new CRUDTableWidgetTextWithLink(
                        '{this->' . Comment::_COMMENT . '}',
                        $this->pathFor(CommentRoutes::ROUTE_NAME_ADMIN_COMMENTS_EDIT, ['comment_id' => '{this->' . Comment::_ID . '}'])
                    )
                ),
                new CRUDTableColumn(
                    'Адрес страницы',
                    new CRUDTableWidgetText(
                        '{this->' . Comment::_URL . '}'
                    )
                ),
                new CRUDTableColumn(
                    'Создан',
                    new CRUDTableWidgetTimestamp('{this->' . Comment::_CREATED_AT_TS . '}')
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInvisible(self::FILTER_NAME_PARENT_ID, null),
            ],
            Comment::_ID . ' DESC'
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof Response) {
            return $crud_form_response;
        }

        $content_html = $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Комментарии');
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', ConfWrapper::value('skif_main_page', '/admin')),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);


        return PhpRender::renderLayout($response, ConfWrapper::value('layout.admin'), $layout_dto);
    }
}
