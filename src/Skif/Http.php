<?php

namespace Skif;


class Http {

    public static function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    static public function redirect301($url)
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

    public static function redirect404()
    {
        header("HTTP/1.0 404 Not Found");
        \Skif\Http::errorPageAction(404);
        exit;
    }

    static public function exit404If($exit_condition)
    {
        if (!$exit_condition) {
            return;
        }

        \Skif\Http::exit404();
    }

    static public function exit404()
    {
        header("HTTP/1.0 404 Not Found");
        \Skif\Http::errorPageAction(404);
        exit();
    }

    static public function exit403()
    {
        header("HTTP/1.0 403 Forbidden");
        \Skif\Http::errorPageAction(403);
        exit();
    }

    static public function exit403If($exit_condition)
    {
        if (!$exit_condition) {
            return;
        }

        \Skif\Http::exit403();
    }

    public static function errorPageAction($error_code)
    {
        echo \Skif\PhpTemplate::renderTemplate(
            'errors/404.html'
        );
    }
}