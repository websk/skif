<?php

declare(strict_types=1);

error_reporting(E_ALL);

set_error_handler(
    static function (int $severity, string $message, string $file, int $line): bool {
        if (($severity & (E_DEPRECATED | E_USER_DEPRECATED)) === 0) {
            return false;
        }

        throw new \ErrorException($message, 0, $severity, $file, $line);
    }
);

require dirname(__DIR__) . '/vendor/autoload.php';
