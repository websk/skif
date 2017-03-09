<?php
namespace Skif\Sitemap;

interface InterfaceSitemapController
{
    /**
     * @return array|\Generator
     */
    public function getUrlsForSitemap();
}
