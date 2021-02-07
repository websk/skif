<?php

namespace WebSK\Skif;

use WebSK\Config\ConfWrapper;
use WebSK\Utils\Assert;
use WebSK\Utils\Url;
use WebSK\Views\ViewsPath;

/**
 * Class SkifPath
 * @package WebSK\Skif
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
    public static function getAppPath()
    {
        return __DIR__;
    }

    /**
     * @return string
     */
    public static function getRootPath()
    {
        return self::getAppPath() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
    }

    /**
     * @return string
     */
    public static function getSkifAssetsPath()
    {
        return self::getRootPath() . DIRECTORY_SEPARATOR . self::PUBLIC_DIR_NAME . DIRECTORY_SEPARATOR . self::ASSETS_DIR_NAME;
    }

    /**
     * @return string
     */
    public static function getViewsPath()
    {
        return self::getRootPath() . DIRECTORY_SEPARATOR . ViewsPath::VIEWS_DIR_NAME;
    }

    /**
     * @param $resource
     * @return string
     */
    public static function wrapUrlPath($resource)
    {
        $url_path = ConfWrapper::value('skif.url_path');

        return Url::appendLeadingSlash(ltrim($url_path, '/') . Url::appendLeadingSlash($resource));
    }

    /**
     * @param $resource
     * @return string
     */
    public static function wrapAssetsVersion($resource)
    {
        $assets_version = ConfWrapper::value('skif.assets_version', 1);

        return self::wrapUrlPath('/' . self::ASSETS_DIR_NAME . '/'. $assets_version . Url::appendLeadingSlash($resource));
    }

    /**
     * @return string
     */
    public static function getMainPage()
    {
        return ConfWrapper::value('skif.main_page', '/admin');
    }

    /**
     * @return string
     */
    public static function getLayout()
    {
        $layout = ConfWrapper::value('skif.layout');

        Assert::assert($layout);

        return $layout;
    }

    /**
     * @return array
     */
    public static function getMenuArr()
    {
        return ConfWrapper::value('skif.menu', []);
    }
}
