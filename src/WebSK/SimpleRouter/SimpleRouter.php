<?php

namespace WebSK\SimpleRouter;

use WebSK\Skif\Sitemap\InterfaceSitemapController;
use WebSK\Skif\Sitemap\InterfaceSitemapBuilder;
use WebSK\Utils\Redirects;
use WebSK\Utils\Url;

/**
 * Class SimpleRouter
 * @package WebSK\SimpleRouter
 */
class SimpleRouter
{
    const CONTINUE_ROUTING = 'CONTINUE_ROUTING';
    const GET_URL = 'GET_URL';
    const GET_METHOD = 'GET_METHOD';
    const EXECUTE_ACTION = 'EXECUTE_ACTION';
    const DEFAULT_CACHE_SEC = 60;

    /**
     * @var object|null
     * текущий (т.е. последний созданный) объект контроллера
     */
    public static $current_controller_obj = null;

    /**
     * @var string
     * текущий адрес запроса
     */
    public static $current_url = '';

    /**
     * @var object|null
     * текущий (т.е. последний созданный) объект экшена
     */
    protected static $current_action_obj = null;

    /** @var string */
    protected static $url_prefix = '';

    /** @var string|null */
    protected static $current_url_by_cli = null;

    /** @var null|InterfaceSitemapBuilder */
    protected static $sitemap_builder_obj = null;

    /** @var array */
    protected static $sitemap_controller_names_arr = [];

    /**
     * @param string $url
     */
    public static function setCurrentUrlByCli(string $url)
    {
        self::$current_url_by_cli = $url;
    }

    /**
     * @param InterfaceSitemapBuilder $sitemap_builder
     */
    public static function setSitemapBuilder(InterfaceSitemapBuilder $sitemap_builder)
    {
        self::$sitemap_builder_obj = $sitemap_builder;
    }

    /**
     * @return null|object
     */
    public static function getCurrentActionObj()
    {
        return self::$current_action_obj;
    }

    /**
     * @return null|object
     */
    public static function getCurrentControllerObj()
    {
        return SimpleRouter::$current_controller_obj;
    }

    /**
     * @return int
     */
    protected static function getDefaultCacheLifetime()
    {
        return self::DEFAULT_CACHE_SEC;
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

    /**
     * @param string $url_regexp
     * @param string $controller_class_name
     * @param string $action_method_name
     * @param int|null $cache_time
     * @param string|null $layout_file
     */
    public static function staticRoute(
        string $url_regexp,
        string $controller_class_name,
        string $action_method_name,
        int $cache_time = null,
        string $layout_file = null
    ) {
        $matches_arr = array();
        self::$current_url = Url::getUriNoQueryString();

        if (!preg_match($url_regexp, self::$current_url, $matches_arr)) {
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

        if ($layout_file) {
            $decoded_matches_arr[] = $layout_file;
        }

        self::$current_controller_obj = new $controller_class_name();
        $action_result = call_user_func_array(
            array(self::$current_controller_obj, $action_method_name),
            $decoded_matches_arr
        );

        if ($action_result == null) {
            exit;
        }

        if ($action_result != SimpleRouter::CONTINUE_ROUTING) {
            exit;
        }

        // сбрасываем текущий контроллер - он больше не актуален
        self::$current_controller_obj = null;
    }

    /**
     * @param string $base_url
     * @param string $controller_class_name
     */
    public static function routeBasedCrud(string $base_url, string $controller_class_name)
    {
        $current_url_no_query = Url::getUriNoQueryString();

        if (!preg_match('@^' . $base_url . '?(.+)@i', $current_url_no_query, $matches_arr)) {
            return;
        }

        SimpleRouter::staticRoute('@^' . $base_url . '/add$@', $controller_class_name, 'addAction', 0);
        SimpleRouter::staticRoute('@^' . $base_url . '/create$@', $controller_class_name, 'createAction', 0);
        SimpleRouter::staticRoute('@^' . $base_url . '/edit/(.+)$@', $controller_class_name, 'editAction', 0);
        SimpleRouter::staticRoute('@^' . $base_url . '/save/(.+)$@i', $controller_class_name, 'saveAction', 0);
        SimpleRouter::staticRoute('@^' . $base_url . '/delete/(\d+)$@i', $controller_class_name, 'deleteAction', 0);
        SimpleRouter::staticRoute('@^' . $base_url . '$@i', $controller_class_name, 'listAction', 0);
    }

    /**
     * @param string $url_mask
     * @param string $target_url
     */
    protected function routeRedirect(string $url_mask, string $target_url)
    {
        $current_url = $_SERVER['REQUEST_URI'];
        if (preg_match($url_mask, $current_url)) {
            Redirects::redirect($target_url);
        }
    }
}
