<?php

namespace Websk\Skif;

use WebSK\Slim\ConfWrapper;
use WebSK\Utils\Url;
use WebSK\Views\ViewsPath;

/**
 * Class SkifPath
 * @package Websk\Skif
 */
class SkifPath
{
    const PUBLIC_DIR_NAME = 'public';
    const ASSETS_DIR_NAME = 'assets';
    const SRC_DIR_NAME = 'src';
    const WEBSK_SKIF_NAMESPACE_DIR = 'WebSK' . DIRECTORY_SEPARATOR . 'Skif';

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
    public static function getSkifRootPath()
    {
        return self::getSkifAppPath() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
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
        return self::getSkifRootPath() . DIRECTORY_SEPARATOR . ViewsPath::VIEWS_DIR_NAME;
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
}
