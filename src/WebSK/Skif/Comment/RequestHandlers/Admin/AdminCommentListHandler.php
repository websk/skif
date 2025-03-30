<?php

namespace WebSK\Skif\Comment\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
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
use WebSK\Skif\SkifPath;
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
    const string FILTER_NAME_PARENT_ID = 'parent_id';

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $crud_table_obj = $this->crud_service->createTable(
            Comment::class,
            $this->crud_service->createForm(
                'comment_create_rand324324',
                new Comment(),
                [
                    new CRUDFormRow('Комментарий', new CRUDFormWidgetTextarea(Comment::_COMMENT, true)),
                    new CRUDFormRow('Адрес страницы', new CRUDFormWidgetInput(Comment::_URL, false, true))
                ]
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(Comment::_ID)),
                new CRUDTableColumn(
                    'Комментарий',
                    new CRUDTableWidgetTextWithLink(
                        Comment::_COMMENT,
                        function (Comment $comment) {
                            return $this->urlFor(AdminCommentEditHandler::class, ['comment_id' => $comment->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Адрес страницы',
                    new CRUDTableWidgetText(
                        Comment::_URL
                    )
                ),
                new CRUDTableColumn(
                    'Создан',
                    new CRUDTableWidgetTimestamp(Comment::_CREATED_AT_TS)
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInvisible(self::FILTER_NAME_PARENT_ID, null),
            ],
            Comment::_CREATED_AT_TS . ' DESC'
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Комментарии');
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);


        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
