<?php

namespace WebSK\Skif\Users;

use WebSK\Skif\SkifPhpRender;

/**
 * Class UserComponents
 * @package WebSK\Skif\Users
 * @deprecated
 */
class UserComponents
{
    public static function renderLoginForm($destination)
    {
        $content = SkifPhpRender::renderTemplateBySkifModule(
            'Users',
            'login_form_block.tpl.php',
            array('destination' => $destination)
        );

        return $content;
    }
}
