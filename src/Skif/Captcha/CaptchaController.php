<?php

namespace Skif\Captcha;

class CaptchaController
{
    static public function mainAction($action)
    {
        switch ($action) {
            case 'check_ajax':
                print Captcha::checkAjax();
                break;
            default:
                Captcha::render();
                break;
        }

        exit;
    }
}
