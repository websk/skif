<?php

namespace Skif\Users;

use Skif\PhpTemplate;

class UserComponents
{
    public static function renderLoginForm($destination)
    {
        $content = PhpTemplate::renderTemplateBySkifModule(
            'Users',
            'login_form_block.tpl.php',
            array('destination' => $destination)
        );

        return $content;
    }
}
