<?php

namespace Skif\Cache\Engines;

interface CacheEngineInterface
{
    public static function set($key, $value, $exp);

    public static function increment($key);

    public static function get($key);

    public static function delete($key);

    public static function getKey($key);
}