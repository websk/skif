<?php

namespace Skif\Logger;

use Skif\Utils;

class LoggerUtils
{

    public static function getLoggerUrlByObject($obj)
    {
        return '/admin/logger/object_log/' . urlencode(Utils::getFullObjectId($obj));
    }
}
