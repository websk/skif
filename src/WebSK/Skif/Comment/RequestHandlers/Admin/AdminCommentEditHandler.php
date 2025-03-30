<?php

namespace WebSK\Skif\Comment\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
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
use WebSK\Skif\Comment\CommentService;
use WebSK\Skif\SkifPath;
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
    const string FILTER_NAME_PARENT_ID = 'parent_id';
    const string PARAM_DESTINATION = 'destination';

    /** @Inject */
    protected CommentService $comment_service;

    /** @Inject */
    protected CRUD $crud_service;


    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $comment_id
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, int $comment_id): ResponseInterface
    {
        $comment_obj = $this->comment_service->getById($comment_id, false);

        if (!$comment_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $destination = $this->urlFor(
            AdminCommentEditHandler::class,
            ['comment_id' => $comment_id]
        );
        if ($request->getParam(self::PARAM_DESTINATION)) {
            $destination = $request->getParam(self::PARAM_DESTINATION);
        }

        $crud_form = $this->crud_service->createForm(
            'comment_edit_rand234234',
            $comment_obj,
            [
                new CRUDFormRow(
                    'Ответ к комментарию',
                    new CRUDFormWidgetReferenceAjax(
                        Comment::_PARENT_ID,
                        Comment::class,
                        Comment::_COMMENT,
                        $this->urlFor(AdminCommentListAjaxHandler::class),
                        $this->urlFor(
                            AdminCommentEditHandler::class,
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
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();

        $new_comment_obj = new Comment();
        $new_comment_obj->setUrl($comment_obj->getUrl());
        $new_comment_obj->setParentId($comment_id);

        $crud_table_obj = $this->crud_service->createTable(
            Comment::class,
            $this->crud_service->createForm(
                'comment_create_rand45654',
                $new_comment_obj,
                [
                    new CRUDFormRow('Комментарий', new CRUDFormWidgetTextarea(Comment::_COMMENT)),
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(Comment::_URL)),
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(Comment::_PARENT_ID))
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
                new CRUDTableFilterEqualInvisible(self::FILTER_NAME_PARENT_ID, $comment_id),
            ]
        );

        $crud_form_table_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_table_response instanceof ResponseInterface) {
            return $crud_form_table_response;
        }

        $content_html .= '<h3>Ответы</h3>';
        $content_html .= $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Комментарии. Комментарий ' . $comment_id);
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO('Комментарии', $this->urlFor(AdminCommentListHandler::class)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);


        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
