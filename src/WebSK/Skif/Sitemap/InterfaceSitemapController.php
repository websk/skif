<?php

namespace WebSK\Skif\Sitemap;

/**
 * Interface InterfaceSitemapController
 * @package WebSK\Skif\Sitemap
 */
interface InterfaceSitemapController
{
    /**
     * @return array|\Generator
     */
    public function getUrlsForSitemap();
}
