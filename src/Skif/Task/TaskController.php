<?php

namespace Skif\Task;


class TaskController extends \Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\Task\Task';

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/task';
    }


}