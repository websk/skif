<?php

namespace WebSK\Skif\Comment;

use WebSK\Skif\CRUD\CRUDController;
use WebSK\Captcha\Captcha;
use WebSK\Utils\Messages;
use WebSK\Utils\Exits;
use WebSK\Utils\Redirects;
use WebSK\Views\PhpRender;

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

    public static function listWebAction()
    {
        if (!array_key_exists('url', $_REQUEST)) {
            echo '';
        }

        $url = $_REQUEST['url'];

        $page = array_key_exists('page', $_REQUEST) ? $_REQUEST['page'] : 1;

        $comments_ids_arr = CommentUtils::getCommentsIdsArrByUrl($url, $page);

        echo PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Comment',
            'form_add.tpl.php',
            array('url' => $url)
        );

        echo PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Comment',
            'list.tpl.php',
            array('comments_ids_arr' => $comments_ids_arr, 'url' => $url)
        );

        echo PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Comment',
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
        if (!array_key_exists('url', $_POST)) {
            Exits::exit404();
        }

        $url = $_POST['url'];


        if (array_key_exists(Captcha::CAPTCHA_FIELD_NAME, $_POST)) {
            if (!Captcha::checkWithMessage()) {
                Redirects::redirect($url);
            }
        }

        $comment = array_key_exists('comment', $_POST) ? $_POST['comment'] : '';

        if (!$comment) {
            Messages::setError('Не указано сообщение');
            Redirects::redirect($url);
        }

        $user_name = array_key_exists('user_name', $_POST) ? $_POST['user_name'] : '';
        $user_email = array_key_exists('user_email', $_POST) ? $_POST['user_email'] : '';
        $parent_id = array_key_exists('parent_id', $_POST) ? $_POST['parent_id'] : 0;

        $comment_obj = new Comment();
        $comment_obj->setParentId($parent_id);
        $comment_obj->setUrl($url);
        $comment_obj->setUrlMd5(md5($url));
        $comment_obj->setUserName($user_name);
        $comment_obj->setUserEmail($user_email);
        $comment_obj->setComment($comment);
        $comment_obj->save();

        Messages::setMessage('Ваше сообщение добавлено');

        Redirects::redirect($url . '#comments');
    }
}
