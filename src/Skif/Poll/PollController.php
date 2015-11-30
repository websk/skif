<?php


class PollController extends \Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\Poll\Poll';

    public static function getBaseUrl($model_class_name)
    {
        return '/admin/poll';
    }

}