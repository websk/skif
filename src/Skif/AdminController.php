<?php

namespace Skif;


class AdminController
{

    public static function indexAction()
    {
        if (\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            \Skif\Http::redirect('/admin/content/page');
        }

        $layout = \Skif\Conf\ConfWrapper::value('layout.admin');
        if (array_key_exists('noheader', $_REQUEST)) {
            $layout = \Skif\Conf\ConfWrapper::value('layout.empty');
        }

        $content = '<h2>Вход в систему управления</h2>';
        $content .= \Skif\PhpTemplate::renderTemplateBySkifModule('Users', 'login_form.tpl.php', array('destination' => '/admin'));

        echo \Skif\PhpTemplate::renderTemplate(
            $layout,
            array(
                'content' => $content,
                'title' => Conf\ConfWrapper::value('site_name'),
                'keywords' => '',
                'description' => ''
            )
        );
    }
}