<?php

namespace Skif\Form;


class FormController extends \Skif\CRUD\CRUDController
{

    protected static $model_class_name = '\Skif\Form\Form';

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/form';
    }

    protected function viewAction($form_id)
    {
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

    public function sendAction($id)
    {
        $site_email = \Skif\Conf\ConfWrapper::value('site_email');
        $site_url = \Skif\Conf\ConfWrapper::value('site_url');

        $email = $_REQUEST['email'];

        $message = 'E-mail: ' . $email . '<br />';


        $res = \Skif\DB\DBWrapper::readAssoc("SELECT * FROM form_field WHERE form='" . intval($id) . "' ORDER BY num");

        for ($i = 0; $i < count($res); $i++) {
            $f[$i] = $_REQUEST['f_' . $i];
            $field[$i] = $res[$i]['id'];
            $name[$i] = $res[$i]['name'];
            $type[$i] = $res[$i]['type'];
            $status[$i] = $res[$i]['status'];
            $num[$i] = $res[$i]['num'];
            $message .= $name[$i] . ": " . $f[$i] . '<br />';
            if ($status[$i] && !$f[$i]) {
                \Skif\Messages::setError("Вы не указали " . $name[$i]);
                return false;
            }
        }

        if (!\Skif\Captcha\Captcha::checkWithMessage()) {
            return false;
        }

        if (!$email) {
            \Skif\Messages::setError('Вы не указали свой E-mail');
            return false;
        }

        if (!\Skif\Utils::checkEmail($email)) {
            \Skif\Messages::setError('Указан не существующий E-mail');
            return false;
        }


        $row = \Skif\DB\DBWrapper::readAssocRow("SELECT form_name, mail, re FROM form WHERE id=?", array($id));
        $title = $row['form_name'];
        $form_to_mail = $row['mail'];
        $re = $row['re'];

        $to_mail = $form_to_mail ? $form_to_mail : $site_email;

        \Skif\SendMail::mailToUtf8($to_mail, $site_email, 'ИБЦ РХТУ им. Д.И. Менделеева', $title, $message);

        \Skif\SendMail::mailToUtf8('sergey.kulkov@gmail.com', $to_mail, 'ИБЦ РХТУ им. Д.И. Менделеева', $title, $message);
        \Skif\SendMail::mailToUtf8('belkalu@yandex.ru', $to_mail, 'ИБЦ РХТУ им. Д.И. Менделеева', $title, $message);

        \Skif\Messages::setMessage($re);

        $re .= "\n";
        $re .= $to_mail . "\n";
        $re .= "http://" . $site_url . "\n";
        \Skif\SendMail::mailToUtf8($email, $to_mail, 'ИБЦ РХТУ им. Д.И. Менделеева', "Благодарим Вас за отправленную информацию!", $re);

        return true;
    }

}