<?php

namespace Skif\Rating;

class RatingController extends \Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\Rating\Rating';
    public static $poll_cookie_prefix = 'rating_star_';

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/rating';
    }

    public static function getRateUrl($rating_id)
    {
        return '/rating/' . $rating_id . '/rate';
    }

    /**
     * Голосование
     * @param $rating_id
     */
    public static function rateAction($rating_id)
    {
        $poll_question_id = isset($_REQUEST['poll_question_id']) ? intval($_REQUEST['poll_question_id']) : '';

        $poll_obj = \Skif\Poll\Poll::factory($rating_id);

        if ($_COOKIE[self::$poll_cookie_prefix . $rating_id] == 'no') {
            \Skif\Messages::setError('Вы уже проголосовали ранее!');

            \Skif\Http::redirect($poll_obj->getUrl());
        }

        if (!empty($poll_question_id)) {
            $poll_question_obj = \Skif\Poll\PollQuestion::factory($poll_question_id);

            $votes = $poll_question_obj->getVotes() + 1;
            $poll_question_obj->setVotes($votes);
            $poll_question_obj->save();

            setcookie(self::$poll_cookie_prefix . $rating_id, 'no', time() + 3600 * 24 * 365);

            \Skif\Messages::setMessage('Спасибо, ваш голос учтен!');
        } else {
            \Skif\Messages::setError('Вы не проголосовали, т.к. не выбрали ответ.');
        }

        \Skif\Http::redirect($poll_obj->getUrl());
    }


}