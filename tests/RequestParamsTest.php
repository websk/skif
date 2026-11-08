<?php

declare(strict_types=1);

namespace WebSK\Skif\Tests;

use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use WebSK\CRUD\RequestParams;

final class RequestParamsTest extends TestCase
{
    public function testReadsQueryValuesWithoutLosingFalseyValues(): void
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/')
            ->withQueryParams(['zero' => '0', 'empty' => '', 'items' => ['1', '2']]);

        self::assertSame('0', RequestParams::query($request, 'zero', 'fallback'));
        self::assertSame('', RequestParams::query($request, 'empty', 'fallback'));
        self::assertSame(['1', '2'], RequestParams::query($request, 'items'));
        self::assertSame('fallback', RequestParams::query($request, 'missing', 'fallback'));
    }

    public function testReadsArrayAndObjectParsedBodies(): void
    {
        $factory = new ServerRequestFactory();
        $array_request = $factory->createServerRequest('POST', '/')->withParsedBody(['id' => 7]);
        $object_request = $factory->createServerRequest('POST', '/')->withParsedBody((object)['id' => 8]);

        self::assertSame(7, RequestParams::parsedBody($array_request, 'id'));
        self::assertSame(8, RequestParams::parsedBody($object_request, 'id'));
        self::assertSame('fallback', RequestParams::parsedBody($array_request, 'missing', 'fallback'));
    }
}
