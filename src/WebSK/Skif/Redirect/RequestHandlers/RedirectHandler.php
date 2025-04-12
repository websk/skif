<?php

namespace WebSK\Skif\Redirect\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Redirect\Redirect;
use WebSK\Skif\Redirect\RedirectServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Url;

/**
 * Class RedirectHandler
 * @package WebSK\Skif\Redirect\RequestHandlers
 */
class RedirectHandler extends BaseHandler
{
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
    public function redirectAction(): string
    {
        $uri = rawurldecode($_SERVER['REQUEST_URI']);
        $exact_uri = $uri;

        $redirect_service = RedirectServiceProvider::getRedirectService($this->container);

        // Check for "string" redirect presence

        $exact_redirect_ids_arr = $redirect_service->getIdsArrBySrcAndKind($exact_uri, Redirect::REDIRECT_KIND_STRING);

        if (!empty($exact_redirect_ids_arr)) {
            $exact_redirect_id = array_shift($exact_redirect_ids_arr);
            $exact_redirect_obj = $redirect_service->getById($exact_redirect_id);

            $http_response_code = $exact_redirect_obj->getCode() ?: StatusCodeInterface::STATUS_MOVED_PERMANENTLY;
            header('Location: ' . Url::appendLeadingSlash($exact_redirect_obj->getDst()), true, $http_response_code);
            exit;
        }

        // Check for "regexp" redirect presence

        $regexp_redirect_ids_arr = $redirect_service->getRegexpIdsArr();

        foreach ($regexp_redirect_ids_arr as $regexp_redirect_id) {
            $regexp_redirect_obj = $redirect_service->getById($regexp_redirect_id);

            $matches = [];

            if (preg_match($regexp_redirect_obj->getSrc(), $uri, $matches)) {
                $dst = $regexp_redirect_obj->getDst();
                foreach ($matches as $match_k => $match_val) {
                    $dst = str_replace('$' . $match_k, $match_val, $dst);
                }

                if ($regexp_redirect_obj->getCode()) {
                    header('Location: ' . Url::appendLeadingSlash($dst), true, $regexp_redirect_obj->getCode());
                    exit;
                }
            }
        }

        return SimpleRouter::CONTINUE_ROUTING;
    }
}
