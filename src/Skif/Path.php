<?php

namespace Skif;

use Skif\Conf\ConfWrapper;

class Path
{
    const ASSETS_DIR_NAME = 'assets';
    const VIEWS_DIR_NAME = 'views';
    const SRC_DIR_NAME = 'src';
    const SKIF_NAMESPACE = 'Skif';


    public static function getRootSitePath()
    {
        return dirname(dirname(dirname(dirname(dirname(__DIR__)))));
    }

    public static function getSkifAppPath()
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . self::SKIF_NAMESPACE;
    }

    public static function getSkifAssetsPath()
    {
        return dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . self::ASSETS_DIR_NAME;
    }

    /**
     * @return string
     */
    public static function getSkifViewsPath()
    {
        return dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . self::VIEWS_DIR_NAME;
    }

    /**
     * @return string
     */
    public static function getSiteViewsPath()
    {
        return self::getRootSitePath() . DIRECTORY_SEPARATOR . self::VIEWS_DIR_NAME;
    }

    /**
     * @return string
     */
    public static function getSiteSrcPath()
    {
        return self::getRootSitePath() . DIRECTORY_SEPARATOR . self::SRC_DIR_NAME;
    }

    /**
     * @param $resource
     * @return string
     */
    public static function wrapSkifUrlPath($resource)
    {
        $skifUrlPath = ConfWrapper::value('skif_url_path');

        return $skifUrlPath . Utils::appendLeadingSlash($resource);
    }

    /**
     * @param $resource
     * @return string
     */
    public static function wrapSkifAssetsVersion($resource)
    {
        $skifAssetsVersion = ConfWrapper::value('skif_assets_version', 1);

        return self::wrapSkifUrlPath('/' . self::ASSETS_DIR_NAME . '/'. $skifAssetsVersion . Utils::appendLeadingSlash($resource));
    }

    /**
     * @param $resource
     * @return string
     */
    public static function wrapAssetsVersion($resource)
    {
        $assetsVersion = ConfWrapper::value('assets_version', 1);
        $assetsUrlPath = ConfWrapper::value('assets_url_path', self::ASSETS_DIR_NAME);

        return Utils::appendLeadingSlash($assetsUrlPath . '/'. $assetsVersion . Utils::appendLeadingSlash($resource));
    }
}