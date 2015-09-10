<?php

namespace Skif\Comments;


class CommentsController
{

    public static function listAction()
    {
        if (!array_key_exists('url', $_REQUEST)) {
            echo '';
        }

        $url = $_REQUEST['url'];

        $page = array_key_exists('page', $_REQUEST) ? $_REQUEST['page'] : 1;

        $comments_ids_arr = \Skif\Comments\CommentsUtils::getCommentsIdsArrByUrl($url, $page);

        echo \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Comments',
            'form_add.tpl.php',
            array('url' => $url)
        );

        echo \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Comments',
            'list.tpl.php',
            array('comments_ids_arr' => $comments_ids_arr, 'url' => $url)
        );

        echo \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Comments',
            'pager.tpl.php',
            array('url' => $url, 'page' => $page)
        );

    }

    /**
     * Добавление / Сохранение комментария
     * @param null $comment_id
     */
    public static function saveAction($comment_id = null)
    {
        if (!array_key_exists('url', $_REQUEST)) {
            \Skif\Http::redirect404();
        }

        $url = $_REQUEST['url'];

        $comment = array_key_exists('comment', $_REQUEST) ? $_REQUEST['comment'] : '';

        if (!$comment) {
            \Skif\Http::redirect($url);
        }

        $comment = nl2br($comment);

        $query = "INSERT INTO comments SET parent_id=?, url=?, url_md5=?, user_id=?, user_name=?, user_email=?, comment=?, date_time=NOW()";

        $user_id = \Skif\Users\AuthUtils::getCurrentUserId();

        if (!$user_id) {
            if (!\Skif\Captcha\Captcha::checkWithMessage()) {
                \Skif\Http::redirect($url);
            }
        }

        $user_name = array_key_exists('user_name', $_REQUEST) ? $_REQUEST['user_name'] : '';
        $user_email = array_key_exists('user_email', $_REQUEST) ? $_REQUEST['user_email'] : '';
        $parent_id = array_key_exists('parent_id', $_REQUEST) ? $_REQUEST['parent_id'] : null;

        $param_arr = array($parent_id, $url, md5($url), $user_id, $user_name, $user_email, $comment);
        \Skif\DB\DBWrapper::query($query, $param_arr);

        $comment_id = \Skif\DB\DBWrapper::lastInsertId();

        if ($parent_id) {
            \Skif\Factory::removeObjectFromCache('\Skif\Comments\Comment', $parent_id);

            if (\Skif\Conf\ConfWrapper::value('comments.send_answer_to_email') && $user_email) {
                $parent_comment_obj = \Skif\Comments\CommentFactory::loadComment($parent_id);
                if ($parent_comment_obj) {
                    $site_email = \Skif\Conf\ConfWrapper::value('site_email');
                    $site_url = \Skif\Conf\ConfWrapper::value('site_url');
                    $site_name = \Skif\Conf\ConfWrapper::value('site_name');

                    $mail_message = 'Здравствуйте, ' . $user_name . '!<br />';
                    $mail_message .= 'Получен ответ на ваше сообщение:<br />';
                    $mail_message .= $parent_comment_obj->getComment() . '<br />';
                    $mail_message .= 'Ответ: ' . $comment . '<br />';
                    $mail_message .= $site_name . ', ' . $site_url;

                    $subject = 'Ответ на сообщение на сайте' . $site_name;
                    \Skif\SendMail::mailToUtf8($user_email, $site_email, $site_name, $subject, $mail_message);
                }
            }

        }

        \Skif\Factory::removeObjectFromCache('\Skif\Comments\Comment', $comment_id);

        \Skif\Http::redirect($url . '#comments');
    }

    /**
     * Удаление комментария
     * @param $comment_id
     */
    public static function deleteAction($comment_id)
    {
        if (!\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            \Skif\Http::redirect404();
        }

        if (!$comment_id) {
            \Skif\Http::redirect404();
        }

        $comment_obj = \Skif\Comments\CommentFactory::loadComment($comment_id);
        if (!$comment_obj) {
            \Skif\Http::redirect404();
        }

        $url = $comment_obj->getUrl();

        $comment_obj->delete();

        \Skif\Http::redirect($url . '#comments');
    }

} 