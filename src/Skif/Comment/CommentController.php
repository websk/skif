<?php

namespace Skif\Comment;


class CommentController extends \Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\Comment\Comment';

    public static function getBaseUrl($model_class_name)
    {
        return '/admin/comments';
    }

    public static function listWebAction()
    {
        if (!array_key_exists('url', $_REQUEST)) {
            echo '';
        }

        $url = $_REQUEST['url'];

        $page = array_key_exists('page', $_REQUEST) ? $_REQUEST['page'] : 1;

        $comments_ids_arr = \Skif\Comment\CommentUtils::getCommentsIdsArrByUrl($url, $page);

        echo \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Comment',
            'form_add.tpl.php',
            array('url' => $url)
        );

        echo \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Comment',
            'list.tpl.php',
            array('comments_ids_arr' => $comments_ids_arr, 'url' => $url)
        );

        echo \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Comment',
            'pager.tpl.php',
            array('url' => $url, 'page' => $page)
        );

    }

    /**
     * Добавление / Сохранение комментария
     * @param null $comment_id
     */
    public static function saveWebAction($comment_id = null)
    {
        if (!array_key_exists('url', $_REQUEST)) {
            \Skif\Http::redirect404();
        }

        $url = $_REQUEST['url'];


        $user_id = \Skif\Users\AuthUtils::getCurrentUserId();

        if (array_key_exists('captcha', $_REQUEST)) {
            if (!\Skif\Captcha\Captcha::checkWithMessage()) {
                \Skif\Http::redirect($url);
            }
        }

        $comment = array_key_exists('comment', $_REQUEST) ? $_REQUEST['comment'] : '';

        if (!$comment) {
            \Skif\Http::redirect($url);
        }

        $comment = nl2br($comment);

        $user_name = array_key_exists('user_name', $_REQUEST) ? $_REQUEST['user_name'] : '';
        $user_email = array_key_exists('user_email', $_REQUEST) ? $_REQUEST['user_email'] : '';
        $parent_id = array_key_exists('parent_id', $_REQUEST) ? $_REQUEST['parent_id'] : null;

        $comment_obj = new \Skif\Comment\Comment();
        $comment_obj->setParentId($parent_id);
        $comment_obj->setUrl($url);
        $comment_obj->setUrlMd5(md5($url));
        $comment_obj->setUserId($user_id);
        $comment_obj->setUserName($user_name);
        $comment_obj->setUserEmail($user_email);
        $comment_obj->setComment($comment);
        $comment_obj->save();

        $comment_id = $comment_obj->getId();

        \Skif\Messages::setMessage('Ваше сообщение добавлено');

        if ($parent_id) {
            \Skif\Factory::removeObjectFromCache('\Skif\Comment\Comment', $parent_id);

            if (\Skif\Conf\ConfWrapper::value('comments.send_answer_to_email') && $user_email) {
                $parent_comment_obj = \Skif\Comment\CommentFactory::loadComment($parent_id);
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

        \Skif\Factory::removeObjectFromCache('\Skif\Comment\Comment', $comment_id);

        \Skif\Http::redirect($url . '#comments');
    }

    /**
     * Удаление комментария
     * @param $comment_id
     */
    public static function deleteWebAction($comment_id)
    {
        \Skif\Http::exit403If(!\Skif\Users\AuthUtils::currentUserIsAdmin());

        \Skif\Http::exit404If(!$comment_id);

        $comment_obj = \Skif\Comment\Comment::factory($comment_id, false);
        \Skif\Http::exit404If(!$comment_obj);

        $redirect_url = $comment_obj->getUrl();

        $comment_obj->delete();

        \Skif\Http::redirect($redirect_url . '#comments');
    }

} 