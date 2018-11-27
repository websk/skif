<?php

namespace Websk\Skif;

use Skif\Utils;
use WebSK\Slim\ConfWrapper;
use WebSK\Utils\Url;

/**
 * Class Path
 * @package Websk\Skif
 */
class Path
{
    const PUBLIC_DIR_NAME = 'public';
    const ASSETS_DIR_NAME = 'assets';
    const VIEWS_DIR_NAME = 'views';
    const SRC_DIR_NAME = 'src';
    const SKIF_NAMESPACE = 'Skif';
    const WEBSK_SKIF_NAMESPACE = 'WebSK/Skif';

    /**
     * @return string
     */
    public static function getRootSitePath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
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
    public static function getWebSKSkifAppPath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . self::WEBSK_SKIF_NAMESPACE;
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

        return ltrim($skifUrlPath, '/') . Url::appendLeadingSlash($resource);
    }

    /**
     * @param $resource
     * @return string
     */
    public static function wrapSkifAssetsVersion($resource)
    {
        $skifAssetsVersion = ConfWrapper::value('skif_assets_version', 1);

        return self::wrapSkifUrlPath('/' . self::ASSETS_DIR_NAME . '/'. $skifAssetsVersion . Url::appendLeadingSlash($resource));
    }

    /**
     * @param $resource
     * @return string
     */
    public static function wrapAssetsVersion($resource)
    {
        $assetsVersion = ConfWrapper::value('assets_version', 1);
        $assetsUrlPath = ConfWrapper::value('assets_url_path', self::ASSETS_DIR_NAME);

        return Url::appendLeadingSlash($assetsUrlPath . '/' . $assetsVersion . Url::appendLeadingSlash($resource));
    }
}
