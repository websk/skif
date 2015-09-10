<?php

namespace Skif\Captcha;


class CaptchaController
{
    static public function mainAction($action)
    {
        switch ($action) {
            case 'check_ajax':
                print \Skif\Captcha\Captcha::checkAjax();
                break;
            default:
                \Skif\Captcha\Captcha::render();
                break;
        }

        exit;
    }
} 