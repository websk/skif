<?php

namespace Skif\Poll;

use WebSK\Slim\ConfWrapper;
use Skif\CRUD\CRUDController;
use Websk\Skif\Messages;
use Skif\PhpTemplate;
use WebSK\Utils\Exits;
use WebSK\Utils\Redirects;

class PollController extends CRUDController
{

    protected static $model_class_name = '\Skif\Poll\Poll';
    public static $poll_cookie_prefix = 'poll_access_';

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/poll';
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

        $poll_obj = Poll::factory($poll_id);

        $cookie_key = self::$poll_cookie_prefix . $poll_id;

        if (isset($_COOKIE[$cookie_key]) && ($_COOKIE[$cookie_key] == 'no')) {
            Messages::setError('Вы уже проголосовали ранее!');

            Redirects::redirect($poll_obj->getUrl());
        }

        if (!empty($poll_question_id)) {
            $poll_question_obj = PollQuestion::factory($poll_question_id);

            $votes = $poll_question_obj->getVotes() + 1;
            $poll_question_obj->setVotes($votes);
            $poll_question_obj->save();

            setcookie($cookie_key, 'no', time() + 3600 * 24 * 365);

            Messages::setMessage('Спасибо, ваш голос учтен!');
        } else {
            Messages::setError('Вы не проголосовали, т.к. не выбрали ответ.');
        }

        Redirects::redirect($poll_obj->getUrl());
    }

    /**
     * Результаты опроса
     * @param $poll_id
     */
    public static function viewAction($poll_id)
    {
        $poll_obj = Poll::factory($poll_id, false);
        Exits::exit404If(!$poll_obj);

        $content = PhpTemplate::renderTemplateBySkifModule(
            'Poll',
            'view.tpl.php',
            array('poll_id' => $poll_id)
        );

        echo PhpTemplate::renderTemplate(
            ConfWrapper::value('layout.main'),
            array(
                'title' => $poll_obj->getTitle(),
                'content' => $content,
            )
        );
    }
}