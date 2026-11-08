<?php

declare(strict_types=1);

namespace WebSK\Skif\SiteMenu;

final class SiteMenuPathMatcher
{
    public static function isActive(string $current_url, string $menu_url): bool
    {
        $current_path = self::normalizePath($current_url);
        $menu_path = self::normalizePath($menu_url, true);

        if ($current_path === null || $menu_path === null) {
            return false;
        }

        if ($menu_path === '/') {
            return $current_path === '/';
        }

        return $current_path === $menu_path || str_starts_with($current_path, $menu_path . '/');
    }

    private static function normalizePath(string $url, bool $reject_external_url = false): ?string
    {
        $url_parts = parse_url($url);
        if ($url_parts === false || ($reject_external_url && isset($url_parts['host']))) {
            return null;
        }

        $path = $url_parts['path'] ?? null;
        if (!is_string($path) || $path === '') {
            return null;
        }

        $normalized_path = '/' . ltrim($path, '/');
        $normalized_path = preg_replace('#/+#', '/', $normalized_path);
        if (!is_string($normalized_path)) {
            return null;
        }

        return $normalized_path === '/' ? '/' : rtrim($normalized_path, '/');
    }
}
