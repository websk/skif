<?php

namespace Skif\Util;

/**
 * Class FilterFactory
 * @package Skif\Util
 */
class FilterFactory
{
    /**
     * @param string$filter_str
     * @return Filter
     */
    public static function getFilter(string $filter_str)
    {
        return new Filter($filter_str);
    }
}