<?php

namespace Skif\Poll;

class PollQuestionController extends \WebSK\Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\Poll\PollQuestion';

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/poll_question';
    }
}
