<?php

namespace Skif\Users;


class UserComponents
{
    public static function renderLoginForm($destination)
    {
        $content = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'login_form_block.tpl.php',
            array('destination' => $destination)
        );

        return $content;
    }
}