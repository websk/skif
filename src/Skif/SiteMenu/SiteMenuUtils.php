<?php

namespace Skif\SiteMenu;


use WebSK\Utils\Url;

class SiteMenuUtils
{
    public static function getSiteMenuIdsArr()
    {
        $query = "SELECT id FROM site_menu ORDER BY name";
        return \Websk\Skif\DBWrapper::readColumn($query);
    }

    public static function getSiteMenuItemIdsArr($site_menu_id, $parent_id = 0)
    {
        $query = "SELECT id FROM site_menu_item WHERE menu_id=? AND parent_id=? ORDER BY weight";
        return \Websk\Skif\DBWrapper::readColumn($query, array($site_menu_id, $parent_id));
    }


    public static function getCurrentSiteMenuItemId()
    {
        $url = Url::getUriNoQueryString();

        return self::getSiteMenuItemIdByUrl($url);
    }

    public static function getSiteMenuItemIdByUrl($url)
    {
        $query = "SELECT id FROM site_menu_item WHERE url=? LIMIT 1";
        $site_menu_item_id = \Websk\Skif\DBWrapper::readField($query, array($url));

        return $site_menu_item_id;
    }

}