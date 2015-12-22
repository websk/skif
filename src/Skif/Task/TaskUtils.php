<?php

namespace Skif\Task;


class TaskUtils
{

    public static function getStatusTitlesArr()
    {
        return  array(
            \Skif\Task\Task::TASK_STATUS_NEW => 'новая',
            \Skif\Task\Task::TASK_STATUS_INPROGRESS => 'в работе',
            \Skif\Task\Task::TASK_STATUS_DEFERRED => 'отложенная',
            \Skif\Task\Task::TASK_STATUS_FINISHED => 'выполненная',
        );
    }

    public static function getTitleByStatus($status)
    {
        $titles_arr = self::getStatusTitlesArr();

        if (array_key_exists($status, $titles_arr)) {
            return $titles_arr[$status];
        }

        return '';
    }
}