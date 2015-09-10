<?php

namespace Skif\Util;

class FilterFactory {
    static public function getFilter($filter_str){
        return new \Skif\Util\Filter($filter_str);
    }
}