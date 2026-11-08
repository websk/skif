<?php

declare(strict_types=1);

namespace WebSK\Skif\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WebSK\Skif\TemplateSanitize;

final class TemplateSanitizeTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function urlProvider(): iterable
    {
        yield 'internal' => ['/admin/content', '/admin/content'];
        yield 'https' => ['https://example.test/path', 'https://example.test/path'];
        yield 'attribute escaping' => ['/search?q="test"', '/search?q=&quot;test&quot;'];
        yield 'javascript' => ['javascript:alert(1)', '#'];
        yield 'data' => ['data:text/html,test', '#'];
        yield 'protocol relative' => ['//evil.example/test', '#'];
    }

    #[DataProvider('urlProvider')]
    public function testUrlAttributeAllowsSafeUrlsAndRejectsExecutableSchemes(string $url, string $expected): void
    {
        self::assertSame($expected, TemplateSanitize::urlAttribute($url));
    }
}
