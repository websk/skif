<?php

declare(strict_types=1);

namespace WebSK\Skif;

use WebSK\Utils\Sanitize;

final class TemplateSanitize
{
    public static function urlAttribute(string $url): string
    {
        $url = trim($url);
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $is_relative = $scheme === null && !str_starts_with($url, '//');
        $is_allowed_absolute = is_string($scheme) && in_array(strtolower($scheme), ['http', 'https', 'mailto'], true);

        if (!$is_relative && !$is_allowed_absolute) {
            return '#';
        }

        return Sanitize::sanitizeAttrValue($url);
    }
}
