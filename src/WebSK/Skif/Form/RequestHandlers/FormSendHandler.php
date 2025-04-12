<?php

namespace WebSK\Skif\Form\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\Auth;
use WebSK\Captcha\Captcha;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\Form\FormFieldService;
use WebSK\Skif\Form\FormService;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Filters;
use WebSK\Utils\Messages;
use WebSK\Utils\Redirects;
use WebSK\Utils\Sanitize;

/**
 * Class FormSendHandler
 * @package WebSK\Skif\Form\RequestHandlers
 */
class FormSendHandler extends BaseHandler
{
    /** @Inject */
    protected FormService $form_service;

    /** @Inject */
    protected FormFieldService $form_field_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $form_id): ResponseInterface
    {
        $form_obj = $this->form_service->getById($form_id, false);
        if (!$form_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $site_name = ConfWrapper::value('site_name');
        $site_email = ConfWrapper::value('site_email');
        $site_domain = ConfWrapper::value('site_domain');

        $user_email = $request->getParam('email');

        $message = 'E-mail: ' . $user_email . '<br>';

        $form_field_ids_arr = $this->form_field_service->getIdsArrByFormId($form_id);

        foreach ($form_field_ids_arr as $form_field_id) {
            $form_field_obj = $this->form_field_service->getById($form_field_id);

            $field_value = $request->getParam('field_' . $form_field_id);

            $name = $form_field_obj->getName();

            $message .= $name . ": " . $field_value . '<br>';

            if ($form_field_obj->isRequired() && !$field_value) {
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

        $form_email = $form_email ?: $site_email;

        $mail = new PHPMailer;
        $mail->CharSet = "utf-8";
        $mail->setFrom($site_email, $site_name);
        $mail->addReplyTo($user_email);
        $mail->addAddress($form_email);
        if ($form_obj->getEmailCopy()) {
            $mail->addAddress($form_obj->getEmailCopy());
        }
        $mail->Subject = $title;
        $mail->Body = $message;
        $mail->AltBody = Sanitize::sanitizeAttrValue($message);
        $mail->send();

        if ($response_mail_message) {
            Messages::setMessage($response_mail_message);
        }

        $response_mail_message .= "<br><br>";
        $response_mail_message .= $form_email . "<br>";
        $response_mail_message .= '<p>' . $site_name . ', <a href="' . $site_domain . '">' . $site_domain . '</a></p>';

        $mail = new PHPMailer;
        $mail->CharSet = "utf-8";
        $mail->setFrom($form_email, $site_name);
        $mail->addReplyTo($form_email);
        $mail->addAddress($user_email);
        $mail->Subject = "Благодарим Вас за отправленную информацию!";
        $mail->Body = $response_mail_message;
        $mail->AltBody = Sanitize::sanitizeAttrValue($response_mail_message);
        $mail->send();

        return $response->withHeader('Location', $form_obj->getUrl());
    }
}
