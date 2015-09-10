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
        header('Location: /error');
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
        exit();
    }

    static public function exit403()
    {
        header("HTTP/1.0 403 Forbidden");
        exit();
    }

    static public function exit403If($exit_condition)
    {
        if (!$exit_condition) {
            return;
        }

        \Skif\Http::exit403();
    }


    public static function errorPageAction()
    {
        header('HTTP/1.1 404 Not Found');

        $title = 'Ошибка 404 . Запрашиваемая Вами страница не существует!';

        $content = '<p>Запрашиваемая Вами страница не существует!
        <br><br><b>Возможные причины:</b><br>
           - Вы неправильно ввели адрес страницы<br>
           - Страница была удалена или перемещена<br>
        <br>Для навигации по сайту воспользуйтесь меню или зайдите на <a href="/">главную страницу</a>.
        </p>';

        echo \Skif\PhpTemplate::renderTemplate(
            \Skif\Conf\ConfWrapper::value('layout.main'),
            array(
                'content' => $content,
                'title' => $title,
                'keywords' => '',
                'description' => ''
            )
        );
    }
}