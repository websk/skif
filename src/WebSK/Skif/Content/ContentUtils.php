<?php

namespace WebSK\Skif\Content;

use WebSK\Slim\Container;
use WebSK\Utils\Url;

/**
 * Class ContentUtils
 * @package WebSK\Skif\Content
 */
class ContentUtils
{
    /**
     * @return int|null
     */
    public static function getCurrentContentId()
    {
        $container = Container::self();

        $content_service = ContentServiceProvider::getContentService($container);

        $content_url = Url::getUriNoQueryString();

        $content_id = $content_service->getIdByUrl($content_url);

        if (!$content_id) {
            return null;
        }

        $content_obj = $content_service->getById($content_id, false);
        if (!$content_obj) {
            return null;
        }

        return $content_id;
    }

    /**
     * @return int|null
     */
    public static function getCurrentRubricId()
    {
        $current_url = Url::getUriNoQueryString();

        $rubric_service = ContentServiceProvider::getRubricService(Container::self());
        $rubric_id = $rubric_service->getIdbyUrl($current_url);

        if (!$rubric_id) {
            return null;
        }

        $rubric_obj = $rubric_service->getById($rubric_id, false);
        if (!$rubric_obj) {
            return null;
        }

        return $rubric_id;
    }


    /**
     * Фильтрация html тегов
     * @param $content
     * @return string
     */
    public static function filterContent($content)
    {
        $allowable_tags_arr = array(
            '<p>',
            '<b><strong><em><i>',
            '<span>',
            '<br>',
            '<div>',
            '<a>',
            '<img>',
            '<h1><h2><h3><h4>',
            '<table><tr><td><tbody><thead><th>',
            '<li><ul><ol>',
            '<script>',
            '<hr>',
            '<form><input><iframe>'
        );

        return strip_tags($content, implode('', $allowable_tags_arr));
    }
}
