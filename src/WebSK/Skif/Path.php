<?php

namespace Websk\Skif;

use WebSK\Slim\ConfWrapper;
use WebSK\Utils\Url;

/**
 * Class Path
 * @package WebSK\Skif
 */
class Path
{
    const PUBLIC_DIR_NAME = 'public';
    const ASSETS_DIR_NAME = 'assets';
    const VIEWS_DIR_NAME = 'views';
    const VIEWS_MODULES_DIR = 'modules';
    const SRC_DIR_NAME = 'src';
    const WEBSK_SKIF_NAMESPACE_DIR = 'WebSK\Skif';

    /**
     * @return string
     */
    public static function getSkifRootPath()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
    }

    /**
     * @return string
     */
    public static function getSkifAppPath()
    {
        return __DIR__;
    }

    /**
     * @return string
     */
    public static function getSkifAssetsPath()
    {
        return self::getSkifRootPath() . DIRECTORY_SEPARATOR . self::PUBLIC_DIR_NAME . DIRECTORY_SEPARATOR . self::ASSETS_DIR_NAME;
    }

    /**
     * @return string
     */
    public static function getSkifViewsPath()
    {
        return self::getSkifRootPath() . DIRECTORY_SEPARATOR . self::VIEWS_DIR_NAME;
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

    /**
     * @return string
     */
    public static function getSiteRootPath()
    {
        return self::getSkifRootPath() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
    }

    /**
     * @return string
     */
    public static function getSiteViewsPath()
    {
        return self::getSiteRootPath() . DIRECTORY_SEPARATOR . self::VIEWS_DIR_NAME;
    }

    /**
     * @return string
     */
    public static function getSiteModulesViewsPath()
    {
        return self::getSiteViewsPath() . DIRECTORY_SEPARATOR . self::VIEWS_MODULES_DIR;
    }
}

