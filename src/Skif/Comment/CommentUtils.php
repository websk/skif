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
    public static function getCommentsIdsArrByUrl($url, $page = 1, $parent_id = null)
    {
        $message_to_page = \Skif\Conf\ConfWrapper::value('comments.message_to_page', 20);
        $start = ($page - 1) * $message_to_page;

        $query = "SELECT id FROM comments WHERE url_md5=?";
        $param_arr = array(md5($url));

        if ($parent_id) {
            $query .= " AND parent_id=?";
            $param_arr[] = $parent_id;
        } else {
            $query .= " AND parent_id IS NULL";
        }

        $query .= " ORDER BY date_time DESC LIMIT " . $start . ', ' . $message_to_page;

        return \Skif\DB\DBWrapper::readColumn($query, $param_arr);
    }

    /**
     * Комментарии к странице
     * @param $url
     * @return array
     */
    public static function getCommentsArrByUrl($url)
    {
        $query = "SELECT id, parent_id, url, user_id, user_name, user_email, comment, date FROM comments WHERE url_md5=?";
        return \Skif\DB\DBWrapper::readObjects($query, array(md5($url)));
    }

    /**
     * Кол-во комментариев к странице
     * @param $url
     * @return array
     */
    public static function getCountCommentsByUrl($url)
    {
        $query = "SELECT count(id) FROM comments WHERE url_md5=? AND parent_id IS NULL";
        return \Skif\DB\DBWrapper::readField($query, array(md5($url)));
    }

    /**
     * Вывод комментариев к странице
     * @param $url
     * @return string
     */
    public static function renderCommentsByUrl($url)
    {
        /*
        if (array_key_exists('load', $_GET)) {
            $query = "SELECT name, mail, message, answer, time FROM talk";
            $res = \Skif\DB\DBWrapper::readObjects($query, array(md5($url)));

            foreach ($res as $obj) {
                $time = date('Y-m-d H:i:s', strtotime($obj->time));
                $query = "INSERT INTO comments SET user_name=?, user_email=?, comment=?, date_time=?";
                \Skif\DB\DBWrapper::query($query, array($obj->name, $obj->mail, $obj->message, $time));

                if (trim($obj->answer)) {
                    $parent_id = \Skif\DB\DBWrapper::lastInsertId();

                    $query = "INSERT INTO comments SET parent_id=?, user_name=?, user_email=?, comment=?, date_time=?";
                    \Skif\DB\DBWrapper::query($query, array($parent_id, $obj->name, $obj->mail, $obj->answer, $time));
                }
            }
        }
        */

        return \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Comment',
            'block.tpl.php',
            array('url' => $url)
        );
    }

} 