<?php

declare(strict_types=1);

namespace WebSK\Skif\Tests;

use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use WebSK\Auth\Auth;
use WebSK\Captcha\Captcha;

final class AuthCaptchaIntegrationTest extends TestCase
{
    public function testPasswordHashCanBeUsedForLoginVerification(): void
    {
        $hash = Auth::hashPassword('php-8.5-test-password');

        self::assertTrue(Auth::verifyPassword('php-8.5-test-password', $hash));
        self::assertFalse(Auth::verifyPassword('wrong-password', $hash));
        self::assertFalse(Auth::passwordNeedsRehash($hash));
    }

    public function testCaptchaImageAndOneTimeValidation(): void
    {
        ob_start();
        Captcha::render();
        $png = ob_get_clean();

        self::assertIsString($png);
        self::assertStringStartsWith("\x89PNG\r\n\x1a\n", $png);

        $code = $_SESSION['websk_captcha_code'] ?? null;
        self::assertIsString($code);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/captcha/check')
            ->withParsedBody(['captcha' => strtoupper($code)]);

        self::assertTrue(Captcha::check($request));
        self::assertFalse(Captcha::check($request));
    }
}
