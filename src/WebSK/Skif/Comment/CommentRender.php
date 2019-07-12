<?php

namespace WebSK\Skif\Comment;

use WebSK\Views\PhpRender;

/**
 * Class CommentRender
 * @package WebSK\Skif\Comment
 */
class CommentRender
{

    /**
     * Вывод комментариев к странице
     * @param $url
     * @return string
     */
    public static function renderCommentsByUrl($url)
    {
        return PhpRender::renderTemplateInViewsDir(
            'block.tpl.php',
            ['url' => $url]
        );
    }
}
