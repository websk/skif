<?php

namespace Skif\Util;

/**
 * Class TB
 * Twitter Bootstrap helper.
 * @package Sportbox\Util
 */
class TB
{
    static public function formGroup($contents)
    {
        return '<div class="form-group">' . $contents . '</div>';
    }

    static public function panel($title, $contents)
    {
        $ret = '<div class="panel panel-default">';
        if ($title) {
            $ret .= '<div class="panel-heading"><h3 class="panel-title">' . $title . '</h3 ></div>';
        }
        $ret .= '<div class="panel-body">' . $contents . '</div ></div>';
        return $ret;
    }

    static public function formInline($contents)
    {
        return '<div class="form-inline">' . $contents . '</div>';
    }


    /**
     * $type can be success, info, warning, danger
     * @param string $message
     * @param string $type
     * @return string
     */
    static public function alert($message, $type = "warning")
    {
        return '<div class="alert alert-'.$type.'">'.$message.'</div>';
    }

}
