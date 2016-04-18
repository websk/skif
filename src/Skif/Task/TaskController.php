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
     * @param string $mail_message
     */
    protected static function sendMessageByTaskObj($task_obj, $mail_message = '')
    {
        $task_id = $task_obj->getId();

        $site_url = \Skif\Conf\ConfWrapper::value('site_url');

        $created_user_obj = \Skif\Users\User::factory($task_obj->getCreatedUserId());

        $site_email = \Skif\Conf\ConfWrapper::value('site_email');
        $site_name = \Skif\Conf\ConfWrapper::value('site_name');

        $current_user_id = \Skif\Users\AuthUtils::getCurrentUserId();

        $mail_message .= '<h2><a href="' . $site_url . static::getEditUrl(self::$model_class_name, $task_id) . '">' . $task_obj->getTitle() . '</a></h2>';
        $mail_message .= '<div>Создана: ' . $task_obj->getCreatedDate() . '</div>';
        $mail_message .= '<div>Создал: ' . $created_user_obj->getName() . '</div>';
        $mail_message .= '<div>Статус: ' . \Skif\Task\TaskUtils::getTitleByStatus($task_obj->getStatus()) . '</div>';
        $mail_message .= '<p>' . $task_obj->getDescriptionTask() . '</p>';
        $mail_message .= '<p>' . $task_obj->getCommentInTask() . '</p>';
        $mail_message .= '<p>' . $site_url . static::getEditUrl(self::$model_class_name, $task_id) . '</p>';

        $subject = 'Задача #' . $task_obj->getId() . ': ' . $task_obj->getTitle();

        if ($task_obj->getCreatedUserId() != $current_user_id) {
            $created_user_obj = \Skif\Users\User::factory($task_obj->getCreatedUserId());

            $mail = new \PHPMailer;
            $mail->CharSet = "utf8";
            $mail->setFrom($site_email, $site_name);
            $mail->addAddress($created_user_obj->getEmail());
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $mail_message;
            $mail->AltBody = \Skif\Utils::checkPlain($mail_message);
            $mail->send();
        }

        if ($task_obj->getAssignedToUserId()) {
            if ($task_obj->getAssignedToUserId() != $current_user_id) {
                $assigned_user_obj = \Skif\Users\User::factory($task_obj->getAssignedToUserId());

                $mail = new \PHPMailer;
                $mail->CharSet = "utf8";
                $mail->setFrom($site_email, $site_name);
                $mail->addAddress($assigned_user_obj->getEmail());
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $mail_message;
                $mail->AltBody = \Skif\Utils::checkPlain($mail_message);
                $mail->send();
            }
        }
    }

    protected static function afterCreate($task_id)
    {
        $task_obj = \Skif\Task\Task::factory($task_id);

        $mail_message = '<p><b>Создана НОВАЯ задача</b></p>';
        \Skif\Task\TaskController::sendMessageByTaskObj($task_obj, $mail_message);
    }

    protected static function afterSave($task_id)
    {
        $task_obj = \Skif\Task\Task::factory($task_id);

        $current_user_id = \Skif\Users\AuthUtils::getCurrentUserId();
        $current_user_obj = \Skif\Users\User::factory($current_user_id);

        $mail_message = '<p><b>Задача была ИЗМЕНЕНА ' . $current_user_obj->getName() . '</b></p>';
        \Skif\Task\TaskController::sendMessageByTaskObj($task_obj, $mail_message);
    }

    /**
     * @param $task_obj \Skif\Task\Task
     */
    protected static function afterDelete($task_obj)
    {
        $current_user_id = \Skif\Users\AuthUtils::getCurrentUserId();
        $current_user_obj = \Skif\Users\User::factory($current_user_id);

        $mail_message = '<p><b>Задача была УДАЛЕНА ' . $current_user_obj->getName() . '</b></p>';
        \Skif\Task\TaskController::sendMessageByTaskObj($task_obj, $mail_message);
    }

}