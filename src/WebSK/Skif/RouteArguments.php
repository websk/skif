<?php

namespace WebSK\Skif;

final class RouteArguments
{
    /**
     * @param array<string, scalar|null> $arguments
     * @return array<string, string>
     */
    public static function path(array $arguments): array
    {
        $normalized = [];
        foreach ($arguments as $name => $value) {
            $normalized[$name] = self::stringify($value);
        }

        return $normalized;
    }

    /**
     * @param array<string, scalar|null|array<array-key, scalar|null>> $arguments
     * @return array<string, string|array<array-key, string>>
     */
    public static function query(array $arguments): array
    {
        $normalized = [];
        foreach ($arguments as $name => $value) {
            if (is_array($value)) {
                $normalized[$name] = array_map(self::stringify(...), $value);
                continue;
            }

            $normalized[$name] = self::stringify($value);
        }

        return $normalized;
    }

    private static function stringify(string|int|float|bool|null $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return (string)$value;
    }
}
