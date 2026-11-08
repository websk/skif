<?php

declare(strict_types=1);

namespace WebSK\Skif\Tests;

use PHPUnit\Framework\TestCase;

final class DeprecationGateTest extends TestCase
{
    public function testDeprecationsAreConvertedToExceptions(): void
    {
        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage('deprecation gate probe');

        trigger_error('deprecation gate probe', E_USER_DEPRECATED);
    }
}
