<?php

namespace WebSK\Skif\Comment\RequestHandlers\Admin;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Table\CRUDTable;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterLikeInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetReferenceSelect;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\Skif\Comment\Comment;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class AdminCommentListAjaxHandler
 * @package WebSK\Skif\Comment\RequestHandlers\Admin
 */
class AdminCommentListAjaxHandler extends BaseHandler
{
    const FILTER_COMMENT = 'comment_23423';

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response)
    {
        $crud_table_obj = CRUDServiceProvider::getCrud($this->container)->createTable(
            Comment::class,
            null,
            [
                new CRUDTableColumn(
                    '',
                    new CRUDTableWidgetReferenceSelect(Comment::_COMMENT)
                ),
                new CRUDTableColumn(
                    'Комментарий',
                    new CRUDTableWidgetText(Comment::_COMMENT)
                )
            ],
            [
                new CRUDTableFilterLikeInline(self::FILTER_COMMENT, '', Comment::_COMMENT, 'Комментарий'),
            ],
            Comment::_CREATED_AT_TS . ' DESC',
            'comment_list',
            CRUDTable::FILTERS_POSITION_INLINE
        );

        return $response->write($crud_table_obj->html($request));
    }
}
