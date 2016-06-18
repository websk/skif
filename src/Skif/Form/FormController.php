<?php

namespace Skif\Form;


class FormController extends \Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\Form\Form';
    protected $url_table = \Skif\Form\Form::DB_TABLE_NAME;

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/form';
    }

    public static function getSendUrl($form_id)
    {
        return '/form/' . $form_id . '/send';
    }

    public function viewAction()
    {
        $form_id = $this->getRequestedId();

        if (!$form_id) {
            return \Skif\UrlManager::CONTINUE_ROUTING;
        }


        $form_obj = \Skif\Form\Form::factory($form_id, false);
        \Skif\Http::exit404If(!$form_obj);

        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Form',
            'view.tpl.php',
            array('form_id' => $form_id)
        );

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.main'),
            array(
                'title' => $form_obj->getTitle(),
                'content' => $content,
            )
        );
    }

    public function sendAction($form_id)
    {
        $site_name = \Skif\Conf\ConfWrapper::value('site_name');
        $site_email = \Skif\Conf\ConfWrapper::value('site_email');
        $site_url = \Skif\Conf\ConfWrapper::value('site_url');

        $user_email = $_REQUEST['email'];

        $message = 'E-mail: ' . $user_email . '<br>';

        $form_obj = \Skif\Form\Form::factory($form_id);

        $form_field_ids_arr = $form_obj->getFormFieldIdsArr();

        foreach ($form_field_ids_arr as $form_field_id) {
            $form_field_obj = \Skif\Form\FormField::factory($form_field_id);

            $field_value = $_REQUEST['field_' . $form_field_id];

            $name = $form_field_obj->getName();

            $message .= $name . ": " . $field_value . '<br>';

            if ($form_field_obj->getStatus() && !$field_value) {
                \Skif\Messages::setError("Вы не указали " . $name);
                \Skif\Http::redirect($form_obj->getUrl());
            }
        }

        $current_user_id = \Skif\Users\AuthUtils::getCurrentUserId();

        if (!$current_user_id) {
            if (!\Skif\Captcha\Captcha::checkWithMessage()) {
                \Skif\Http::redirect($form_obj->getUrl());
            }
        }

        if (!$user_email) {
            \Skif\Messages::setError('Вы не указали свой E-mail');
            \Skif\Http::redirect($form_obj->getUrl());
        }

        if (!\Skif\Utils::checkEmail($user_email)) {
            \Skif\Messages::setError('Указан не существующий E-mail');
            \Skif\Http::redirect($form_obj->getUrl());
        }

        $title = $form_obj->getTitle();
        $form_email = $form_obj->getEmail();
        $response_mail_message = nl2br($form_obj->getResponseMailMessage());

        $to_mail = $form_email ? $form_email : $site_email;

        $mail = new \PHPMailer;
        $mail->CharSet = "utf-8";
        $mail->setFrom($site_email, $site_name);
        $mail->addReplyTo($user_email);
        $mail->addAddress($to_mail);
        if ($form_obj->getEmailCopy()) {
            $mail->addAddress($form_obj->getEmailCopy());
        }
        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body = $message;
        $mail->AltBody = \Skif\Utils::checkPlain($message);
        $mail->send();

        \Skif\Messages::setMessage($response_mail_message);

        $response_mail_message .= "<br><br>";
        $response_mail_message .= $to_mail . "<br>";
        $response_mail_message .= '<p>' . $site_name . ', <a href="' . \Skif\Utils::appendHttp($site_url) . '">' . $site_url . '</a></p>';

        $mail = new \PHPMailer;
        $mail->CharSet = "utf-8";
        $mail->setFrom($to_mail, $site_name);
        $mail->addAddress($user_email);
        $mail->isHTML(true);
        $mail->Subject = "Благодарим Вас за отправленную информацию!";
        $mail->Body = $response_mail_message;
        $mail->AltBody = \Skif\Utils::checkPlain($response_mail_message);
        $mail->send();

        \Skif\Http::redirect($form_obj->getUrl());
    }

}