<?php

namespace Websk\Skif;

use WebSK\Skif\ConfWrapper;
use Skif\Utils;

class Path
{
    const PUBLIC_DIR_NAME = 'public';
    const ASSETS_DIR_NAME = 'assets';
    const VIEWS_DIR_NAME = 'views';
    const SRC_DIR_NAME = 'src';
    const SKIF_NAMESPACE = 'Skif';

    /**
     * @return string
     */
    public static function getRootSitePath()
    {
        return dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
    }

    /**
     * @return string
     */
    public static function getSkifAppPath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . self::SKIF_NAMESPACE;
    }

    /**
     * @return string
     */
    public static function getSkifAssetsPath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . self::PUBLIC_DIR_NAME . DIRECTORY_SEPARATOR . self::ASSETS_DIR_NAME;
    }

    /**
     * @return string
     */
    public static function getSkifViewsPath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . self::VIEWS_DIR_NAME;
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

        return ltrim($skifUrlPath, '/') . Utils::appendLeadingSlash($resource);
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