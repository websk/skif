<?php

namespace Skif\Logger;


class LoggerUtils
{

    public static function getLoggerUrlByObject($obj)
    {
        return '/admin/logger/object_log/' . urlencode(\Skif\Utils::getFullObjectId($obj));
    }
}