<?php

namespace Skif\Sitemap;

/**
 * Interface InterfaceSitemapBuilder
 * @package Skif\Sitemap
 */
interface InterfaceSitemapBuilder
{
    public function add($url, $freq);

    public function log($controller_name);
}
