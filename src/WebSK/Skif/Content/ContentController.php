<?php

namespace WebSK\Skif\Content;

use WebSK\Config\ConfWrapper;
use WebSK\Slim\Container;
use WebSK\SimpleRouter\Sitemap\InterfaceSitemapController;
use WebSK\Utils\Url;

/**
 * Class ContentController
 * @package WebSK\Skif\Content
 */
class ContentController implements InterfaceSitemapController
{

    /**
     * @return array
     */
    public function getUrlsForSitemap(): array
    {
        $current_domain = ConfWrapper::value('site_domain');

        $urls = [];

        $content_service = ContentServiceProvider::getContentService(Container::self());
        $content_type_service = ContentServiceProvider::getContentTypeService(Container::self());

        $content_type_ids_arr = $content_type_service->getAllIdsArrByIdAsc();

        foreach ($content_type_ids_arr as $content_type_id) {
            $content_type_obj = $content_type_service->getById($content_type_id);

            $content_ids_arr = $content_service->getPublishedIdsArrByType($content_type_obj->getType());

            foreach ($content_ids_arr as $content_id) {
                $content_obj = $content_service->getById($content_id);

                $urls[] = ['url' => $current_domain . Url::appendLeadingSlash($content_obj->getUrl())];
            }
        }

        return $urls;
    }
}
