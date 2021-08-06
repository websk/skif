<?php

namespace WebSK\Skif;

use WebSK\Config\ConfWrapper;
use WebSK\Utils\Assert;
use WebSK\Utils\Url;

/**
 * Class SkifPath
 * @package WebSK\Skif
 */
class SkifPath
{
    const ASSETS_DIR_NAME = 'assets';

    /**
     * @param $resource
     * @return string
     */
    public static function wrapUrlPath($resource): string
    {
        $url_path = ConfWrapper::value('skif.url_path');

        return Url::appendLeadingSlash(ltrim($url_path, '/') . Url::appendLeadingSlash($resource));
    }

    /**
     * @param $resource
     * @return string
     */
    public static function wrapAssetsVersion($resource): string
    {
        $assets_version = ConfWrapper::value('skif.assets_version', 1);

        return self::wrapUrlPath('/' . self::ASSETS_DIR_NAME . '/'. $assets_version . Url::appendLeadingSlash($resource));
    }

    /**
     * @return string
     */
    public static function getMainPage(): string
    {
        return ConfWrapper::value('skif.main_page', '/admin');
    }

    /**
     * @return string
     */
    public static function getLayout(): string
    {
        $layout = ConfWrapper::value('skif.layout');

        Assert::assert($layout);

        return $layout;
    }
}
