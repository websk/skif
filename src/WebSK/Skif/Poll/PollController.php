<?php

namespace WebSK\Skif\Poll;

use WebSK\Config\ConfWrapper;
use WebSK\Slim\Container;
use WebSK\Utils\Messages;
use WebSK\Utils\Exits;
use WebSK\Utils\Redirects;
use WebSK\Views\PhpRender;

/**
 * Class PollController
 * @package WebSK\Skif\Poll
 */
class PollController
{

    public static $poll_cookie_prefix = 'poll_access_';

    /**
     * @param int $poll_id
     * @return string
     */
    public static function getVoteUrl(int $poll_id)
    {
        return '/poll/' . $poll_id . '/vote';
    }

    /**
     * @param int $poll_id
     * @return string
     */
    public static function getUrl(int $poll_id)
    {
        return '/poll/' . $poll_id;
    }

    /**
     * Голосование
     * @param $poll_id
     */
    public static function voteAction($poll_id)
    {
        $poll_question_id = isset($_REQUEST['poll_question_id']) ? intval($_REQUEST['poll_question_id']) : '';

        $poll_service = PollServiceProvider::getPollService(Container::self());
        $poll_obj = $poll_service->getById($poll_id);

        $poll_question_service = PollServiceProvider::getPollQuestionService(Container::self());


        $cookie_key = self::$poll_cookie_prefix . $poll_id;

        if (isset($_COOKIE[$cookie_key]) && ($_COOKIE[$cookie_key] == 'no')) {
            Messages::setError('Вы уже проголосовали ранее!');

            Redirects::redirect(self::getUrl($poll_id));
        }

        if (!empty($poll_question_id)) {
            $poll_question_obj = $poll_question_service->getById($poll_question_id);

            $votes = $poll_question_obj->getVotes() + 1;
            $poll_question_obj->setVotes($votes);

            $poll_question_service->save($poll_question_obj);

            setcookie($cookie_key, 'no', time() + 3600 * 24 * 365);

            Messages::setMessage('Спасибо, ваш голос учтен!');
        } else {
            Messages::setError('Вы не проголосовали, т.к. не выбрали ответ.');
        }

        Redirects::redirect(self::getUrl($poll_id));
    }

    /**
     * Результаты опроса
     * @param $poll_id
     */
    public static function viewAction($poll_id)
    {
        $poll_service = PollServiceProvider::getPollService(Container::self());
        $poll_obj = $poll_service->getById($poll_id, false);

        Exits::exit404If(!$poll_obj);

        $content = PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Poll',
            'view.tpl.php',
            array('poll_id' => $poll_id)
        );

        echo PhpRender::renderTemplate(
            ConfWrapper::value('layout.main'),
            array(
                'title' => $poll_obj->getTitle(),
                'content' => $content,
            )
        );
    }
}
