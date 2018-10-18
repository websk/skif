<?php

namespace Skif;

class Http {

    /**
     * @param string $url
     */
    public static function redirect(string $url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * @param string $url
     */
    public static function redirect301(string $url)
    {
        header("HTTP/1.0 301 Moved Permanently");
        header('Location: ' . $url);
        exit;
    }

    public static function cacheHeaders()
    {
        $cache_sec = 60;

        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cache_sec) . ' GMT');
        header('Cache-Control: max-age=' . $cache_sec . ', public');
    }

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
        self::errorPageAction(404);
        exit();
    }

    public static function exit403()
    {
        header("HTTP/1.0 403 Forbidden");
        self::errorPageAction(403);
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

    /**
     * @param int $error_code
     * @deprecated
     */
    protected static function errorPageAction(int $error_code)
    {
        echo PhpTemplate::renderTemplate(
            'errors/error_page.tpl.php',
            array('error_code' => $error_code)
        );
    }
}