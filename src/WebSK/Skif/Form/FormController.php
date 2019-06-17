<?php

namespace WebSK\Skif\Form;

use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Auth\Auth;
use WebSK\Captcha\Captcha;
use WebSK\Skif\CRUD\CRUDController;
use WebSK\Utils\Messages;
use WebSK\Config\ConfWrapper;
use WebSK\Utils\Exits;
use WebSK\Utils\Filters;
use WebSK\Utils\Redirects;
use WebSK\Views\PhpRender;

/**
 * Class FormController
 * @package WebSK\Skif\Form
 */
class FormController extends CRUDController
{

    protected static $model_class_name = Form::class;
    protected $url_table = Form::DB_TABLE_NAME;

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
            return SimpleRouter::CONTINUE_ROUTING;
        }

        $form_obj = Form::factory($form_id, false);
        Exits::exit404If(!$form_obj);

        $content = PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Form',
            'view.tpl.php',
            array('form_id' => $form_id)
        );

        echo PhpRender::renderTemplate(
            ConfWrapper::value('layout.main'),
            array(
                'title' => $form_obj->getTitle(),
                'content' => $content,
            )
        );
    }

    public function sendAction($form_id)
    {
        $site_name = ConfWrapper::value('site_name');
        $site_email = ConfWrapper::value('site_email');
        $site_domain = ConfWrapper::value('site_domain');

        $user_email = $_POST['email'];

        $message = 'E-mail: ' . $user_email . '<br>';

        $form_obj = Form::factory($form_id);

        $form_field_ids_arr = $form_obj->getFormFieldIdsArr();

        foreach ($form_field_ids_arr as $form_field_id) {
            $form_field_obj = FormField::factory($form_field_id);

            $field_value = $_POST['field_' . $form_field_id];

            $name = $form_field_obj->getName();

            $message .= $name . ": " . $field_value . '<br>';

            if ($form_field_obj->getStatus() && !$field_value) {
                Messages::setError("Вы не указали " . $name);
                Redirects::redirect($form_obj->getUrl());
            }
        }

        $current_user_id = Auth::getCurrentUserId();

        if (!$current_user_id) {
            if (!Captcha::checkWithMessage()) {
                Redirects::redirect($form_obj->getUrl());
            }
        }

        if (!$user_email) {
            Messages::setError('Вы не указали свой E-mail');
            Redirects::redirect($form_obj->getUrl());
        }

        if (!Filters::checkEmail($user_email)) {
            Messages::setError('Указан не существующий E-mail');
            Redirects::redirect($form_obj->getUrl());
        }

        $title = $form_obj->getTitle();
        $form_email = $form_obj->getEmail();
        $response_mail_message = nl2br($form_obj->getResponseMailMessage());

        $form_email = $form_email ? $form_email : $site_email;

        $mail = new \PHPMailer;
        $mail->CharSet = "utf-8";
        $mail->setFrom($site_email, $site_name);
        $mail->addReplyTo($user_email);
        $mail->addAddress($form_email);
        if ($form_obj->getEmailCopy()) {
            $mail->addAddress($form_obj->getEmailCopy());
        }
        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body = $message;
        $mail->AltBody = Filters::checkPlain($message);
        $mail->send();

        Messages::setMessage($response_mail_message);

        $response_mail_message .= "<br><br>";
        $response_mail_message .= $form_email . "<br>";
        $response_mail_message .= '<p>' . $site_name . ', <a href="' . $site_domain . '">' . $site_domain . '</a></p>';

        $mail = new \PHPMailer;
        $mail->CharSet = "utf-8";
        $mail->setFrom($form_email, $site_name);
        $mail->addReplyTo($form_email);
        $mail->addAddress($user_email);
        $mail->isHTML(true);
        $mail->Subject = "Благодарим Вас за отправленную информацию!";
        $mail->Body = $response_mail_message;
        $mail->AltBody = Filters::checkPlain($response_mail_message);
        $mail->send();

        Redirects::redirect($form_obj->getUrl());
    }
}
