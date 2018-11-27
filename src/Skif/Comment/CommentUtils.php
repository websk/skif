<?php

namespace Skif\Comment;


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
        $message_to_page = \WebSK\Slim\ConfWrapper::value('comments.message_to_page', 20);
        $start = ($page - 1) * $message_to_page;

        $query = "SELECT id FROM comments
            WHERE url_md5=? AND parent_id=?
            ORDER BY date_time DESC
            LIMIT " . $start . ', ' . $message_to_page;
        $param_arr = array(md5($url), $parent_id);

        return \Websk\Skif\DBWrapper::readColumn($query, $param_arr);
    }

    /**
     * Кол-во комментариев к странице
     * @param $url
     * @return array
     */
    public static function getCountCommentsByUrl($url)
    {
        $query = "SELECT count(id) FROM comments WHERE url_md5=? AND parent_id=0";
        return \Websk\Skif\DBWrapper::readField($query, array(md5($url)));
    }

    /**
     * Вывод комментариев к странице
     * @param $url
     * @return string
     */
    public static function renderCommentsByUrl($url)
    {
        return \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Comment',
            'block.tpl.php',
            array('url' => $url)
        );
    }

} 