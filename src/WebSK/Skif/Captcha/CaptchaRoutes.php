<?php

namespace WebSK\Skif\Captcha;

use Slim\App;
use WebSK\Skif\Captcha\RequestHandlers\CheckCaptchaAjaxHandler;
use WebSK\Skif\Captcha\RequestHandlers\RenderCaptchaHandler;
use WebSK\Skif\HTTP;

class CaptchaRoutes
{
    public static function route(App $app)
    {
        $app->group('/captcha', function (App $app) {
            $app->map(
                [HTTP::METHOD_GET],
                '/generate',
                RenderCaptchaHandler::class
            )->setName(RenderCaptchaHandler::class);

            $app->map(
                [HTTP::METHOD_GET],
                '/check_ajax',
                CheckCaptchaAjaxHandler::class
            )->setName(CheckCaptchaAjaxHandler::class);
        });
    }
}
