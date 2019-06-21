<?php

namespace WebSK\Skif\Comment\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Config\ConfWrapper;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormInvisibleRow;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTextarea;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTimestamp;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInvisible;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTimestamp;
use WebSK\Skif\Comment\Comment;
use WebSK\Skif\Comment\CommentRoutes;
use WebSK\Skif\Comment\CommentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminCommentEditHandler
 * @package WebSK\Skif\Comment\RequestHandlers\Admin
 */
class AdminCommentEditHandler extends BaseHandler
{
    const FILTER_NAME_PARENT_ID = 'parent_id';

    /**
     * @param Request $request
     * @param Response $response
     * @param int $comment_id
     */
    public function __invoke(Request $request, Response $response, int $comment_id)
    {
        $comment_obj = CommentServiceProvider::getCommentService($this->container)
            ->getById($comment_id, false);

        if (!$comment_obj) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        $destination = $this->pathFor(
            CommentRoutes::ROUTE_NAME_ADMIN_COMMENTS_EDIT,
            ['comment_id' => $comment_id]
        );
        if ($request->getParam('destination')) {
            $destination = $request->getParam('destination');
        }

        $crud_form = CRUDServiceProvider::getCrud($this->container)->createForm(
            'comment_edit_rand234234',
            $comment_obj,
            [
                new CRUDFormRow(
                    'Ответ к комментарию',
                    new CRUDFormWidgetReferenceAjax(
                        Comment::_PARENT_ID,
                        Comment::class,
                        Comment::_COMMENT,
                        $this->pathFor(CommentRoutes::ROUTE_NAME_ADMIN_COMMENTS_LIST_AJAX),
                        $this->pathFor(
                            CommentRoutes::ROUTE_NAME_ADMIN_COMMENTS_EDIT,
                            ['comment_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER]
                        )
                    )
                ),
                new CRUDFormRow(
                    'Комментарий',
                    new CRUDFormWidgetTextarea(Comment::_COMMENT, true)
                ),
                new CRUDFormRow(
                    'Адрес страницы',
                    new CRUDFormWidgetInput(Comment::_URL, false, true)
                ),
                new CRUDFormRow(
                    'Имя пользователя',
                    new CRUDFormWidgetInput(Comment::_USER_NAME)
                ),
                new CRUDFormRow(
                    'Email',
                    new CRUDFormWidgetInput(Comment::_USER_EMAIL)
                ),
                new CRUDFormRow(
                    'Создан',
                    new CRUDFormWidgetTimestamp(Comment::_CREATED_AT_TS)
                ),
            ],
            $destination
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof Response) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();

        $new_comment_obj = new Comment();
        $new_comment_obj->setUrl($comment_obj->getUrl());
        $new_comment_obj->setParentId($comment_id);

        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            Comment::class,
            CRUDServiceProvider::getCrud($this->container)->createForm(
                'comment_create_rand45654',
                $new_comment_obj,
                [
                    new CRUDFormRow('Комментарий', new CRUDFormWidgetTextarea(Comment::_COMMENT)),
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(Comment::_URL)),
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(Comment::_PARENT_ID))
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
                new CRUDTableFilterEqualInvisible(self::FILTER_NAME_PARENT_ID, $comment_id),
            ]
        );

        $crud_form_table_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_table_response instanceof Response) {
            return $crud_form_table_response;
        }

        $content_html .= '<h3>Ответы</h3>';
        $content_html .= $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Комментарии. Комментарий ' . $comment_id);
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', ConfWrapper::value('skif_main_page', '/admin')),
            new BreadcrumbItemDTO('Комментарии', $this->pathFor(CommentRoutes::ROUTE_NAME_ADMIN_COMMENTS_LIST)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);


        return PhpRender::renderLayout($response, ConfWrapper::value('layout.admin'), $layout_dto);
    }
}
