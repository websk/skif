<?php

namespace WebSK\Auth\Users;

use WebSK\Views\PhpRender;

/**
 * Class UserComponents
 * @package WebSK\Auth\Users
 * @deprecated
 */
class UserComponents
{
    public static function renderLoginForm($destination)
    {
        $content = PhpRender::renderTemplateByModule(
            'WebSK/Auth/Users',
            'login_form_block.tpl.php',
            ['destination' => $destination]
        );

        return $content;
    }
}
