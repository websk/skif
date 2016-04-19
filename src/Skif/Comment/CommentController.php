<?php

namespace Skif\Comment;


class CommentController extends \Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\Comment\Comment';

    public static function getCRUDBaseUrl($model_class_name)
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
            \Skif\Http::exit404();
        }

        $url = $_REQUEST['url'];


        if (array_key_exists('captcha', $_REQUEST)) {
            if (!\Skif\Captcha\Captcha::checkWithMessage()) {
                \Skif\Http::redirect($url);
            }
        }

        $comment = array_key_exists('comment', $_REQUEST) ? $_REQUEST['comment'] : '';

        if (!$comment) {
            \Skif\Messages::setError('Не указано сообщение');
            \Skif\Http::redirect($url);
        }

        $user_name = array_key_exists('user_name', $_REQUEST) ? $_REQUEST['user_name'] : '';
        $user_email = array_key_exists('user_email', $_REQUEST) ? $_REQUEST['user_email'] : '';
        $parent_id = array_key_exists('parent_id', $_REQUEST) ? $_REQUEST['parent_id'] : 0;

        $comment_obj = new \Skif\Comment\Comment();
        $comment_obj->setParentId($parent_id);
        $comment_obj->setUrl($url);
        $comment_obj->setUrlMd5(md5($url));
        $comment_obj->setUserName($user_name);
        $comment_obj->setUserEmail($user_email);
        $comment_obj->setComment($comment);
        $comment_obj->save();

        \Skif\Messages::setMessage('Ваше сообщение добавлено');

        \Skif\Http::redirect($url . '#comments');
    }

} 