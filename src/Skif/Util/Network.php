<?php

namespace Skif\Util;

class Network
{
    static public function is_private_network($ip)
    {
        if (preg_match("/unknown/", $ip)) {
            return true;
        }
        if (preg_match("/127\.0\./", $ip)) {
            return true;
        }
        if (preg_match("/^192\.168\./", $ip)) {
            return true;
        }
        if (preg_match("/^10\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.16\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.17\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.18\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.19\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.20\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.21\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.22\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.23\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.24\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.25\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.26\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.27\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.28\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.29\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.30\./", $ip)) {
            return true;
        }
        if (preg_match("/^172\.31\./", $ip)) {
            return true;
        }

        return false;
    }

    static public function getClientIpXff()
    {
        //$ip = $_SERVER['REMOTE_ADDR'];
        $remote_addr = $_SERVER['REMOTE_ADDR'];

        if (array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER) && $_SERVER['HTTP_X_FORWARDED_FOR']) {

            $list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            // TODO: move private network determination to helper
            foreach ($list as $ip) {
                if (self::is_private_network($ip)) {
                    break;
                }

                $remote_addr = $ip;
            }
        }

        // TODO: move substitutions to helper, make cleaner logic
        // substitute ip for local network (shabolovka)
        if (preg_match("/127\.0\./", $remote_addr)) {
            $remote_addr = "80.247.45.128";
        }

        if (preg_match("/192\.168\./", $remote_addr)) {
            $remote_addr = "80.247.45.128";
        }

        return $remote_addr;
    }

    static public function getClientIpRemoteAddr()
    {
        $remote_addr = $_SERVER['REMOTE_ADDR'];

        // substitute ip for local network (shabolovka)
        if (preg_match("/127\.0\./", $remote_addr)) {
            $remote_addr = "80.247.45.128";
        }

        if (preg_match("/192\.168\./", $remote_addr)) {
            $remote_addr = "80.247.45.128";
        }

        return $remote_addr;
    }

    static public function get_country_code($remote_addr = '')
    {
        if (empty($remote_addr)) {
            $remote_addr = $_SERVER['REMOTE_ADDR'];
        }

        $country_networks = \Skif\CountryNetworks::factory();
        if ($country_networks && ($country_code = $country_networks->match($remote_addr))) {
            return $country_code;
        }

        $country_code = geoip_country_code_by_name($remote_addr);

        if (preg_match("/^192\.168\./", $remote_addr) || preg_match("/^10\./", $remote_addr) || preg_match("/^127\./",
                $remote_addr)) {
            $country_code = 'RU';
        }

        if (\Skif\Helpers::check_fifa_addresses($remote_addr)) {
            $country_code = 'RU';
        }

        return $country_code;
    }

    static public function ipInBlackList($remote_addr)
    {
        return false;

        $cache_key = 'video_networks_blacklist';
        $networks_arr = \Websk\Skif\CacheWrapper::get($cache_key);

        if ($networks_arr === false) {
            $networks_arr = \Skif\Util\KeyValue::get("video_networks_blacklist", null);
            \Websk\Skif\CacheWrapper::set($cache_key, $networks_arr, 30);
        }

        if (!$networks_arr) {
            return false;
        }

        if (!is_array($networks_arr)) {
            return false;
        }

        foreach ($networks_arr as $value) {
            list ($net, $mask) = explode('/', $value);
            if (!$mask) {
                $mask = 32;
            }
            if ((ip2long($remote_addr) & ~((1 << (32 - $mask)) - 1)) == ip2long($net)) {
                return true;
            }
        }

        return false;
    }
}