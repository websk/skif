<?php

namespace Skif\Poll;

class PollController extends \Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\Poll\Poll';
    public static $poll_cookie_prefix = 'poll_access_';

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/poll';
    }

    public static function getViewUrl($poll_id)
    {
        return '/poll/' . $poll_id;
    }

    public static function getVoteUrl($poll_id)
    {
        return '/poll/' . $poll_id . '/vote';
    }

    /**
     * Голосование
     * @param $poll_id
     */
    public static function voteAction($poll_id)
    {
        $poll_question_id = isset($_REQUEST['poll_question_id']) ? intval($_REQUEST['poll_question_id']) : '';

        if ($_COOKIE[self::$poll_cookie_prefix . $poll_id] == 'no') {
            \Skif\Messages::setError('Вы уже проголосовали ранее!');

            \Skif\Http::redirect(self::getViewUrl($poll_id));
        }

        if (!empty($poll_question_id)) {
            $poll_question_obj = \Skif\Poll\PollQuestion::factory($poll_question_id);

            $votes = $poll_question_obj->getVotes() + 1;
            $poll_question_obj->setVotes($votes);
            $poll_question_obj->save();

            setcookie(self::$poll_cookie_prefix . $poll_id, 'no', time() + 3600 * 24 * 365);

            \Skif\Messages::setMessage('Спасибо, ваш голос учтен!');
        } else {
            \Skif\Messages::setError('Вы не проголосовали, т.к. не выбрали ответ.');
        }

        \Skif\Http::redirect(self::getViewUrl($poll_id));
    }

    /**
     * Результаты опроса
     * @param $poll_id
     */
    public static function viewAction($poll_id)
    {
        $poll_obj = \Skif\Poll\Poll::factory($poll_id);

        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Poll',
            'view.tpl.php',
            array('poll_id' => $poll_id)
        );;

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.main'),
            array(
                'title' => $poll_obj->getTitle(),
                'content' => $content,
            )
        );
    }
}