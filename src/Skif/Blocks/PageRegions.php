<?php

namespace Skif\Blocks;

class PageRegions
{
    /**
     * @param string $region
     * @param string $theme
     * @param string $page_url
     * @return string
     */
    public static function renderBlocksByRegion($region, $theme, $page_url = '')
    {
        return \Skif\Blocks\PageRegionsUtils::renderBlocksByPageRegionNameAndTemplateName($region, $theme);
    }

}