<?php

declare(strict_types=1);

namespace WebSK\Skif\Tests;

use PHPUnit\Framework\TestCase;
use WebSK\Skif\RouteArguments;

final class RouteArgumentsTest extends TestCase
{
    public function testNormalizesPathArgumentsForSlim(): void
    {
        self::assertSame(
            ['id' => '42', 'enabled' => '1', 'missing' => ''],
            RouteArguments::path(['id' => 42, 'enabled' => true, 'missing' => null])
        );
    }

    public function testNormalizesQueryArgumentsForSlim(): void
    {
        self::assertSame(
            ['page' => '2', 'ids' => ['10', '20'], 'enabled' => '0'],
            RouteArguments::query(['page' => 2, 'ids' => [10, 20], 'enabled' => false])
        );
    }
}
