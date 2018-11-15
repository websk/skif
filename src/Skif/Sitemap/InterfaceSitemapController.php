<?php

namespace Skif\Sitemap;

/**
 * Interface InterfaceSitemapController
 * @package Skif\Sitemap
 */
interface InterfaceSitemapController
{
    /**
     * @return array|\Generator
     */
    public function getUrlsForSitemap();
}
