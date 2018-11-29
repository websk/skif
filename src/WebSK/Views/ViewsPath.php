<?php

namespace WebSK\Views;

use WebSK\Slim\ConfWrapper;

/**
 * Class ViewsPath
 * @package WebSK\Views
 */
class ViewsPath
{
    public const VIEWS_DIR_NAME = 'views';
    public const VIEWS_MODULES_DIR = 'modules';

    /**
     * @return string
     */
    public static function getSiteViewsPath()
    {
        return ConfWrapper::value('site_path') . DIRECTORY_SEPARATOR . ViewsPath::VIEWS_DIR_NAME;
    }

    /**
     * @return string
     */
    public static function getSiteModulesViewsPath()
    {
        return ViewsPath::getSiteViewsPath() . DIRECTORY_SEPARATOR . ViewsPath::VIEWS_MODULES_DIR;
    }
}
