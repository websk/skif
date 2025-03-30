<?php

namespace WebSK\Skif\Comment;

use WebSK\Views\PhpRender;

/**
 * Class CommentComponents
 * @package WebSK\Skif\Comment
 */
class CommentComponents
{

    /**
     * Вывод комментариев к странице
     * @param string $url
     * @return string
     */
    public static function renderCommentsByUrl(string $url): string
    {
        return PhpRender::renderTemplateInViewsDir(
            'block.tpl.php',
            ['url' => $url]
        );
    }
}
