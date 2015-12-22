<?php

namespace Skif\Task;


class TaskController extends \Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\Task\Task';

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/task';
    }

    /**
     * @param $task_obj \Skif\Task\Task
     * @return string
     */
    protected static function getMailMessageByTaskObj($task_obj)
    {
        $task_id = $task_obj->getId();

        $site_url = \Skif\Conf\ConfWrapper::value('site_url');

        $created_user_obj = \Skif\Users\User::factory($task_obj->getCreatedUserId());

        $mail_message = '<h2><a href="' . $site_url . static::getEditUrl(self::$model_class_name, $task_id) . '">' . $task_obj->getTitle() . '</a></h2>';
        $mail_message .= '<div>Создана: ' . $task_obj->getCreatedDate() . '</div>';
        $mail_message .= '<div>Создал: ' . $created_user_obj->getName() . '</div>';
        $mail_message .= '<p>' . $task_obj->getDescriptionTask() . '</p>';
        $mail_message .= '<p>' . $site_url . static::getEditUrl(self::$model_class_name, $task_id) . '</p>';

        return $mail_message;
    }

    protected static function afterCreate($task_id)
    {
        $task_obj = \Skif\Task\Task::factory($task_id);

        if ($task_obj->getAssignedToUserId()) {
            $assigned_user_obj = \Skif\Users\User::factory($task_obj->getAssignedToUserId());

            if ($assigned_user_obj->getEmail()) {
                $site_email = \Skif\Conf\ConfWrapper::value('site_email');
                $site_name = \Skif\Conf\ConfWrapper::value('site_name');

                $mail_message = \Skif\Task\TaskController::getMailMessageByTaskObj($task_obj);

                $subject = 'Задача #' . $task_obj->getId() . ': ' . $task_obj->getTitle();
                \Skif\SendMail::mailToUtf8($assigned_user_obj->getEmail(), $site_email, $site_name, $subject, $mail_message);
            }
        }
    }

    /**
     * @param $task_obj \Skif\Task\Task
     */
    protected static function afterDelete($task_obj)
    {
        $site_email = \Skif\Conf\ConfWrapper::value('site_email');
        $site_name = \Skif\Conf\ConfWrapper::value('site_name');

        $current_user_id = \Skif\Users\AuthUtils::getCurrentUserId();
        $current_user_obj = \Skif\Users\User::factory($current_user_id);

        $mail_message = '<p><b>Задача была удалена' . $current_user_obj->getName() . '</b></p>';
        $mail_message .= \Skif\Task\TaskController::getMailMessageByTaskObj($task_obj);

        $subject = 'Задача #' . $task_obj->getId() . ': ' . $task_obj->getTitle();

        if ($task_obj->getCreatedUserId() != $current_user_id) {
            $created_user_obj = \Skif\Users\User::factory($task_obj->getCreatedUserId());

            \Skif\SendMail::mailToUtf8($created_user_obj->getEmail(), $site_email, $site_name, $subject, $mail_message);
        }

        if ($task_obj->getAssignedToUserId()) {
            if ($task_obj->getAssignedToUserId() != $current_user_id) {
                $assigned_user_obj = \Skif\Users\User::factory($task_obj->getAssignedToUserId());
                \Skif\SendMail::mailToUtf8($assigned_user_obj->getEmail(), $site_email, $site_name, $subject, $mail_message);
            }
        }
    }

}