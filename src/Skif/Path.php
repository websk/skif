<?php

namespace Skif;


class Path
{
    const ASSETS_DIR_NAME = 'assets';
    const VIEWS_DIR_NAME = 'views';


    public static function getRootSitePath()
    {
        return dirname(dirname(dirname(dirname(dirname(__DIR__)))));
    }

    public static function getSkifAppPath()
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Skif';
    }

    public static function getSkifAssetsPath()
    {
        return dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . self::ASSETS_DIR_NAME;
    }

    public static function getSkifViewsPath()
    {
        return dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . self::VIEWS_DIR_NAME;
    }

    public static function getSiteViewsPath()
    {
        return \Skif\Path::getRootSitePath() . DIRECTORY_SEPARATOR . self::VIEWS_DIR_NAME;
    }
}