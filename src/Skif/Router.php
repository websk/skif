<?php

namespace Skif;

use Skif\Sitemap\InterfaceSitemapController;
use Skif\Sitemap\InterfaceSitemapBuilder;
use WebSK\Utils\Url;

/**
 * Class Router
 * @package Skif
 */
class Router
{
    const CONTINUE_ROUTING = 'CONTINUE_ROUTING';

    const GET_URL = 'GET_URL';
    const GET_METHOD = 'GET_METHOD';
    const EXECUTE_ACTION = 'EXECUTE_ACTION';

    protected static $current_action_obj = null; // текущий (т.е. последний созданный) объект экшена

    // TODO: describe, add getter+setter
    protected static $url_prefix = '';

    protected static $current_url_by_cli = null;

    public static function setCurrentUrlByCli($url)
    {
        self::$current_url_by_cli = $url;
    }

    /**
     * @var null|InterfaceSitemapBuilder
     */
    protected static $sitemap_builder_obj = null;

    protected static $sitemap_controller_names_arr = array();

    public static function setSitemapBuilder(InterfaceSitemapBuilder $sitemap_builder)
    {
        self::$sitemap_builder_obj = $sitemap_builder;
    }

    /**
     *
     * Если текущий урл удовлетворяет переданной маске, то вызывается переданный экшен контроллера с параметрами,
     * если таковые предусмотрены маской.
     *
     * Если роутеру был предустановлен текущий урл, то вместо вызова экшена выкидывается исключение,
     * в сообщении которого передаётся название контроллера, экшена и параметры. Исключение обрабатывается скриптом
     * cli.php -> 5 (определение контроллера по урлу).
     *
     * @param string $url_regexp
     * @param callable $callback_arr
     * @param int|null $cache_seconds_for_headers
     * @throws \Exception
     */
    public static function route($url_regexp, callable $callback_arr, $cache_seconds_for_headers = null)
    {
        list($controller_obj_or_class_name, $action_method_name) = $callback_arr;

        if (is_object($controller_obj_or_class_name)) {
            $controller_obj = $controller_obj_or_class_name;
            $controller_class_name = get_class($controller_obj);
        } else {
            $controller_class_name = $controller_obj_or_class_name;
            $controller_obj = new $controller_class_name();
        }

        // Добавление ссылок в сайтмап для консольного скрипта построения сайтмапа
        if (self::$sitemap_builder_obj instanceof InterfaceSitemapBuilder) {
            self::addControllerUrlsToSitemap($controller_class_name);
            return;
        }

        $matches_arr = array();
        $current_url = self::$current_url_by_cli !== null ? self::$current_url_by_cli : Url::getUriNoQueryString();

        if (!preg_match($url_regexp, $current_url, $matches_arr)) {
            return;
        }

        if (count($matches_arr)) {
            // убираем первый элемент массива - содержит всю сматченую строку
            array_shift($matches_arr);
        }

        $decoded_matches_arr = array();
        foreach ($matches_arr as $arg_value) {
            $decoded_matches_arr[] = urldecode($arg_value);
        }

        // Обрабатываемая ошибка для консольного скрипта роутинга (определение контроллера по урлу)
        if (self::$current_url_by_cli !== null) {
            throw new \Exception(
                $controller_class_name.'->'.$action_method_name.'('.implode(',', $decoded_matches_arr).')'
            );
        }

        // кэширование страницы по умолчанию
        if (is_null($cache_seconds_for_headers)) {
            $cache_seconds_for_headers = self::getDefaultCacheLifetime();
        }
        self::cacheHeaders($cache_seconds_for_headers);

        $action_result = call_user_func_array(array($controller_obj, $action_method_name), $decoded_matches_arr);

        if ($action_result != self::CONTINUE_ROUTING) {
            exit;
        }
    }

    /**
     * Простой метод проверки, соответствует ли запрошенный урл указанной маске.
     * Может использоваться для группировки роутов.
     * @param $url_regexp
     * @return bool
     */
    public static function matchGroup($url_regexp)
    {
        if (self::$sitemap_builder_obj instanceof InterfaceSitemapBuilder) {
            return true;
        }

        $current_url = self::$current_url_by_cli !== null ? self::$current_url_by_cli : Url::getUriNoQueryString();

        if (!preg_match($url_regexp, $current_url)) {
            return false;
        }

        return true;
    }

    public static function getCurrentActionObj()
    {
        return self::$current_action_obj;
    }

    /**
     * @return int
     */
    protected static function getDefaultCacheLifetime()
    {
        return 60;
    }

    /**
     * @param int $seconds
     */
    public static function cacheHeaders($seconds = 0)
    {
        if (php_sapi_name() !== "cli") {
            return;
        }

        if ($seconds) {
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $seconds) . ' GMT');
            header('Cache-Control: max-age=' . $seconds . ', public');
        } else {
            header('Expires: ' . gmdate('D, d M Y H:i:s', date('U') - 86400) . ' GMT');
            header('Cache-Control: no-cache');
        }
    }

    /**
     * @param $controller_class_name
     */
    protected static function addControllerUrlsToSitemap($controller_class_name)
    {
        if (in_array($controller_class_name, self::$sitemap_controller_names_arr)) {
            return;
        }

        self::$sitemap_controller_names_arr[] = $controller_class_name;
        $controller_obj = new $controller_class_name();
        if ($controller_obj instanceof InterfaceSitemapController) {
            self::$sitemap_builder_obj->log($controller_class_name);
            foreach ($controller_obj->getUrlsForSitemap() as $url_info_arr) {
                if (!array_key_exists('url', $url_info_arr)) {
                    continue;
                }

                $url = $url_info_arr['url'];
                $freq = array_key_exists('freq', $url_info_arr) ? $url_info_arr['freq'] : 'never';
                self::$sitemap_builder_obj->add($url, $freq);
            }
        }

        return;
    }
}
