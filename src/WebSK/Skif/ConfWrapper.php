<?php

namespace WebSK\Skif;

use Websk\Slim\Container;

/**
 * Class ConfWrapper
 * @package WebSK\Skif
 */
class ConfWrapper
{
    const SETTINGS_CONTAINER_ID = 'settings';

    /**
     * Get value an array by using "root.branch.leaf" notation
     *
     * @param string $path Path to a specific option to extract
     * @param mixed $default Value to use if the path was not found
     * @return mixed
     */
    public static function value($path, $default = '')
    {
        if (empty($path)) {
            return '';
        }

        $container = Container::self();

        $value = $container[self::SETTINGS_CONTAINER_ID] ?? [];

        $parts = explode(".", $path);

        foreach ($parts as $part) {
            if (isset($value[$part])) {
                $value = $value[$part];
            } else {
                // key doesn't exist, fail
                return $default;
            }
        }

        return $value;
    }
}
