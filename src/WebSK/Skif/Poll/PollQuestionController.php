<?php

namespace WebSK\Skif\Poll;

use WebSK\Skif\CRUD\CRUDController;

/**
 * Class PollQuestionController
 * @package WebSK\Skif\Poll
 */
class PollQuestionController extends CRUDController
{
    protected static $model_class_name = PollQuestion::class;

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/poll_question';
    }
}
