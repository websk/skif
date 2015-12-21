<?php

namespace Skif\Task;


class TaskController extends \Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\Task\Task';

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/task';
    }

    protected static function afterCreate($task_id)
    {
        $task_obj = \Skif\Task\Task::factory($task_id);

        if ($task_obj->getAssignedToUserId()) {
            $assigned_user_obj = \Skif\Users\User::factory($task_obj->getAssignedToUserId());

            if ($assigned_user_obj->getEmail()) {
                $site_email = \Skif\Conf\ConfWrapper::value('site_email');
                $site_name = \Skif\Conf\ConfWrapper::value('site_name');
                $site_url = \Skif\Conf\ConfWrapper::value('site_url');

                $created_user_obj = \Skif\Users\User::factory($task_obj->getCreatedUserId());

                $mail_message = '';
                $mail_message .= '<h2><a href="' . $site_url . static::getEditUrl(self::$model_class_name, $task_id) . '">' . $task_obj->getTitle() . '</a></h2>';
                $mail_message .= 'Создана: ' . $task_obj->getCreatedDate() . '<br />';
                $mail_message .= 'Создал: ' . $created_user_obj->getName() . '<br />';
                $mail_message .= '<p>' . $task_obj->getDescriptionTask() . '</p>';
                $mail_message .= '<p>' . $site_url . static::getEditUrl(self::$model_class_name, $task_id) . '</p>';


                $subject = 'Задача #' . $task_obj->getId() . ': ' . $task_obj->getTitle();
                \Skif\SendMail::mailToUtf8($assigned_user_obj->getEmail(), $site_email, $site_name, $subject, $mail_message);
            }
        }

    }

}