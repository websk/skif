<?php

namespace Skif\Redirect;

use Websk\Cache\CacheWrapper;
use Skif\CRUD\CRUDController;
use Websk\Skif\DBWrapper;
use Skif\UrlManager;
use Skif\Utils;
use WebSK\Utils\Url;

class RedirectController extends CRUDController
{

    protected static $model_class_name = Redirect::class;

    public static function getCRUDBaseUrl($model_class_name)
    {
        return '/admin/redirect';
    }


    /**
     * Если совпадает несколько правил - используется первое по порядку ID.
     * обработка запроса:
     * - сначала поиск по таблице правил совпадений src и урла для записей типа "строка", если найдено - редирект на dst
     * - при этом используется индекс, поиск быстрый
     * - если совпадения строки не найдено:
     * - выборка всех правил типа "регексп", перебор:
     * - если матч src и урла (с извлечением, если есть в регекспе):
     * - редирект на dst c заменой переменных $1, $2 в dst на извлеченные значения
     *
     * @return string
     * @throws \Exception
     */
    public function redirectAction()
    {
        $uri = rawurldecode($_SERVER['REQUEST_URI']);
        $exact_uri = $uri;

        // Check for "string" redirect presence

        $exact_redirect_stdobj_arr = DBWrapper::readObjects(
            "SELECT dst, code FROM redirect_rewrites WHERE src = ? AND kind = 1",
            array($exact_uri)
        );

        if (!empty($exact_redirect_stdobj_arr)) {
            $exact_redirect_stdobj = array_shift($exact_redirect_stdobj_arr);
            $http_response_code = $exact_redirect_stdobj->code ? $exact_redirect_stdobj->code : 301;
            header('Location: ' . Url::appendLeadingSlash($exact_redirect_stdobj->dst), true, intval($http_response_code));
            exit;
        }


        // Check for "regexp" redirect presence

        $cache_key = self::getCacheKeyRegexpRedirectArr();

        $regexp_redirect_stdobj_arr = CacheWrapper::get($cache_key);

        if ($regexp_redirect_stdobj_arr === false) {
            $regexp_redirect_stdobj_arr = DBWrapper::readObjects(
                "SELECT src, dst, code FROM redirect_rewrites WHERE kind = ? ORDER BY id",
                array(Redirect::REDIRECT_KIND_REGEXP)
            );
            CacheWrapper::set($cache_key, $regexp_redirect_stdobj_arr, 3600);
        }

        foreach ($regexp_redirect_stdobj_arr as $regexp_redirect_stdobj) {
            $matches = array();

            if (preg_match($regexp_redirect_stdobj->src, $uri, $matches)) {

                $dst = $regexp_redirect_stdobj->dst;
                foreach ($matches as $match_k => $match_val) {
                    $dst = str_replace('$' . $match_k, $match_val, $dst);
                }

                if ($regexp_redirect_stdobj->code != "") {
                    header('Location: ' . Url::appendLeadingSlash($dst), true, intval($regexp_redirect_stdobj->code));
                    exit;
                }
            }
        }

        return UrlManager::CONTINUE_ROUTING;
    }

    public static function getCacheKeyRegexpRedirectArr()
    {
        return "regexp_redirect_std_obj_arr";
    }
}
