<?php

declare(strict_types=1);

namespace WebSK\Skif\Tests;

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Slim\Routing\RoutingResults;
use WebSK\Auth\AuthRoutes;
use WebSK\Captcha\CaptchaRoutes;

final class RouteRegistrationTest extends TestCase
{
    public function testAuthAndCaptchaRoutesAreRegistered(): void
    {
        $app = AppFactory::create();
        AuthRoutes::register($app);
        CaptchaRoutes::register($app);

        $parser = $app->getRouteCollector()->getRouteParser();

        self::assertSame('/auth/login_form', $parser->urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN_FORM));
        self::assertSame('/auth/login', $parser->urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGIN));
        self::assertSame('/auth/logout', $parser->urlFor(AuthRoutes::ROUTE_NAME_AUTH_LOGOUT));
        self::assertSame('/captcha/generate', $parser->urlFor(CaptchaRoutes::ROUTE_NAME_CAPTCHA_GENERATE));
    }

    public function testUnknownRouteProducesNotFoundRoutingResult(): void
    {
        $app = AppFactory::create();
        AuthRoutes::register($app);

        $result = $app->getRouteResolver()->computeRoutingResults('/missing', 'GET');

        self::assertSame(RoutingResults::NOT_FOUND, $result->getRouteStatus());
    }
}
