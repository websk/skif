<?php

namespace WebSK\Skif\Users;

use WebSK\Skif\PhpRender;

/**
 * Class UserComponents
 * @package WebSK\Skif\Users
 * @deprecated
 */
class UserComponents
{
    public static function renderLoginForm($destination)
    {
        $content = PhpRender::renderTemplateBySkifModule(
            'Users',
            'login_form_block.tpl.php',
            array('destination' => $destination)
        );

        return $content;
    }
}
