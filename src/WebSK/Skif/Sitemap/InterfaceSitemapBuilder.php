<?php

namespace WebSK\Skif\Sitemap;

/**
 * Interface InterfaceSitemapBuilder
 * @package WebSK\Skif\Sitemap
 */
interface InterfaceSitemapBuilder
{
    public function add($url, $freq);

    public function log($controller_name);
}
