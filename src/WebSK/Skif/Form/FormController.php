<?php

namespace WebSK\Skif\Form;

use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Auth\Auth;
use Websk\Skif\Captcha\Captcha;
use WebSK\Skif\CRUD\CRUDController;
use Websk\Utils\Messages;
use WebSK\Skif\SkifPhpRender;
use WebSK\Slim\ConfWrapper;
use WebSK\Utils\Exits;
use WebSK\Utils\Filters;
use WebSK\Utils\Redirects;
use WebSK\Utils\Url;

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

        $content = SkifPhpRender::renderTemplateBySkifModule(
            'Form',
            'view.tpl.php',
            array('form_id' => $form_id)
        );

        echo SkifPhpRender::renderTemplate(
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
        $site_url = ConfWrapper::value('site_url');

        $user_email = $_REQUEST['email'];

        $message = 'E-mail: ' . $user_email . '<br>';

        $form_obj = Form::factory($form_id);

        $form_field_ids_arr = $form_obj->getFormFieldIdsArr();

        foreach ($form_field_ids_arr as $form_field_id) {
            $form_field_obj = FormField::factory($form_field_id);

            $field_value = $_REQUEST['field_' . $form_field_id];

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
        $mail->AltBody = Filters::checkPlain($message);
        $mail->send();

        Messages::setMessage($response_mail_message);

        $response_mail_message .= "<br><br>";
        $response_mail_message .= $to_mail . "<br>";
        $response_mail_message .= '<p>' . $site_name . ', <a href="' . Url::appendHttp($site_url) . '">' . $site_url . '</a></p>';

        $mail = new \PHPMailer;
        $mail->CharSet = "utf-8";
        $mail->setFrom($to_mail, $site_name);
        $mail->addAddress($user_email);
        $mail->isHTML(true);
        $mail->Subject = "Благодарим Вас за отправленную информацию!";
        $mail->Body = $response_mail_message;
        $mail->AltBody = Filters::checkPlain($response_mail_message);
        $mail->send();

        Redirects::redirect($form_obj->getUrl());
    }
}
