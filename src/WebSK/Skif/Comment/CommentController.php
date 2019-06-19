<?php

namespace WebSK\Skif\Comment;

use WebSK\Skif\CRUD\CRUDController;

/**
 * Class CommentController
 * @package WebSK\Skif\Comment
 */
class CommentController extends CRUDController
{
    protected static $model_class_name = Comment::class;

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/comments';
    }
}
