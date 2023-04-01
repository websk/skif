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
    public static function getCurrentContentId(): ?int
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
    public static function getCurrentRubricId(): ?int
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
}
