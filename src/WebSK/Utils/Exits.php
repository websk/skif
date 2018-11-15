<?php

namespace WebSK\Utils;

/**
 * Class Exits
 * @package WebSK\Utils
 */
class Exits
{

    /**
     * @param $exit_condition
     */
    public static function exit404If($exit_condition)
    {
        if (!$exit_condition) {
            return;
        }

        self::exit404();
    }

    public static function exit404()
    {
        header("HTTP/1.0 404 Not Found");
        exit;
    }

    public static function exit403()
    {
        header("HTTP/1.0 403 Forbidden");
        exit;
    }

    /**
     * @param $exit_condition
     */
    public static function exit403If($exit_condition)
    {
        if (!$exit_condition) {
            return;
        }

        self::exit403();
    }
}
