<?php
namespace Skif\Sitemap;

interface InterfaceSitemapBuilder
{
    public function add($url, $freq);
    public function log($controller_name);
}
