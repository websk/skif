<?php

declare(strict_types=1);

namespace WebSK\Skif\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WebSK\Skif\SiteMenu\SiteMenuPathMatcher;

final class SiteMenuPathMatcherTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string, bool}>
     */
    public static function pathProvider(): iterable
    {
        yield 'root is active only on root' => ['/', '/', true];
        yield 'root is not active on news' => ['/news', '/', false];
        yield 'exact section path is active' => ['/news', '/news', true];
        yield 'section remains active below its path' => ['/news/archive', '/news', true];
        yield 'similar prefix is not treated as a child' => ['/newsletter', '/news', false];
        yield 'query and trailing slash are ignored' => ['/news/?page=2', '/news/', true];
        yield 'external menu URL is not marked active' => ['/news', 'https://example.test/news', false];
    }

    #[DataProvider('pathProvider')]
    public function testActivePathMatching(string $current_url, string $menu_url, bool $expected): void
    {
        self::assertSame($expected, SiteMenuPathMatcher::isActive($current_url, $menu_url));
    }
}
