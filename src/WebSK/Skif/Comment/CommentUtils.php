<?php

namespace WebSK\Skif\Comment;

use WebSK\Skif\DBWrapper;
use WebSK\Skif\SkifPhpRender;
use WebSK\Slim\ConfWrapper;

/**
 * Class CommentUtils
 * @package WebSK\Skif\Comment
 */
class CommentUtils
{
    /**
     * Массив ID комментариев к странице
     * @param $url
     * @param $page
     * @param $parent_id
     * @return array
     */
    public static function getCommentsIdsArrByUrl($url, $page = 1, $parent_id = 0)
    {
        $message_to_page = ConfWrapper::value('comments.message_to_page', 20);
        $start = ($page - 1) * $message_to_page;

        $query = "SELECT id FROM " . Comment::DB_TABLE_NAME . "
            WHERE url_md5=? AND parent_id=?
            ORDER BY date_time DESC
            LIMIT " . $start . ', ' . $message_to_page;
        $param_arr = array(md5($url), $parent_id);

        return DBWrapper::readColumn($query, $param_arr);
    }

    /**
     * Кол-во комментариев к странице
     * @param $url
     * @return array
     */
    public static function getCountCommentsByUrl($url)
    {
        $query = "SELECT count(id) FROM comments WHERE url_md5=? AND parent_id=0";

        return DBWrapper::readField($query, array(md5($url)));
    }

    /**
     * Вывод комментариев к странице
     * @param $url
     * @return string
     */
    public static function renderCommentsByUrl($url)
    {
        return SkifPhpRender::renderTemplateBySkifModule(
            'Comment',
            'block.tpl.php',
            array('url' => $url)
        );
    }
}
