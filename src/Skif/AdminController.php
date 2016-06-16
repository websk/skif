<?php

namespace Skif;


class AdminController
{

    public static function indexAction()
    {
        if (\Skif\Users\AuthUtils::getCurrentUserId()) {
            if (\Skif\Users\AuthUtils::currentUserIsAdmin()) {
                \Skif\Http::redirect('/admin/content/page');
            }

            \Skif\Http::exit403();
        }

        echo \Skif\PhpTemplate::renderTemplate(
            'layouts/layout.admin_login.tpl.php'
        );
    }
}