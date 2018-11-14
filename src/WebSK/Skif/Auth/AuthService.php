<?php

namespace WebSK\Skif\Auth;

use Skif\Utils;
use WebSK\Skif\ConfWrapper;
use WebSK\Skif\Router;

/**
 * Class AuthService
 * @package WebSK\Skif\Users
 */
class AuthService
{
    public function __construct()
    {

    }

    /**
     * @param string $name
     * @param string $email
     * @param int $confirm_code
     * @throws \phpmailerException
     */
    public function sendConfirmMail(string $name, string $email, int $confirm_code)
    {
        $site_email = ConfWrapper::value('site_email');
        $site_domain = ConfWrapper::value('site_domain');
        $site_name = ConfWrapper::value('site_name');

        $confirm_url = $site_domain . Router::pathFor(AuthRoutes::ROUTE_NAME_AUTH_CONFIRM_REGISTRATION, ['confirm_code' => $confirm_code]);

        $mail_message = 'Здравствуйте, ' . $name . '!<br />';
        $mail_message .= '<p>На сайте ' .  $site_domain . ' была создана регистрационная запись, в которой был указал ваш электронный адрес (e-mail).</p>';
        $mail_message .= '<p>Если вы не регистрировались на данном сайте, просто проигнорируйте это сообщение! Аккаунт будет автоматически удален через некоторое время.</p>';
        $mail_message .= '<p>Если это были вы, то для завершения процедуры регистрации, пожалуйста перейдите по ссылке <a href="' . $confirm_url .  '">' . $confirm_url .  '</a></p>';

        $mail_message .= '<p>С уважением, администрация сайта ' . $site_name . ', ' . $site_domain . '</p>';

        $subject = 'Подтверждение регистрации на сайте ' . $site_name;

        $mail = new \PHPMailer;
        $mail->CharSet = "utf-8";
        $mail->setFrom($site_email, $site_name);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $mail_message;
        $mail->AltBody = Utils::checkPlain($mail_message);
        $mail->send();
    }
}