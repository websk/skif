<?php

namespace Skif;


class AdminController
{

    public static function indexAction()
    {
        if (\Skif\Users\AuthUtils::currentUserIsAdmin()) {
            \Skif\Http::redirect('/admin/content/page');
        }

        echo \Skif\PhpTemplate::renderTemplate(
            'layouts/layout.admin_login.tpl.php'
        );
    }
}