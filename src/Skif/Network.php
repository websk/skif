<?php
namespace Skif;


class Network
{
    static public function is_private_network($ip)
    {
        if (preg_match("/unknown/", $ip))
            return true;
        if (preg_match("/127\.0\./", $ip))
            return true;
        if (preg_match("/^192\.168\./", $ip))
            return true;
        if (preg_match("/^10\./", $ip))
            return true;

        return false;
    }

    static public function getClientIpXff()
    {
        $remote_addr = $_SERVER['REMOTE_ADDR'];

        if (array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER) && $_SERVER['HTTP_X_FORWARDED_FOR']) {

            $list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            foreach ($list as $ip) {
                if (self::is_private_network($ip))
                    break;

                $remote_addr = $ip;
            }
        }

        return $remote_addr;
    }

    static public function getClientIpRemoteAddr()
    {
        $remote_addr = $_SERVER['REMOTE_ADDR'];

        return $remote_addr;
    }

    /**
     * Проверка IP на вхождение в диапазон по маске сети
     * @param $ip
     * @param $subnet_mask_arr
     * @return bool
     */
    public static function checkIpBySubnetMask($ip, $subnet_mask_arr)
    {
        foreach ($subnet_mask_arr as $network) {
            if (empty($network)) {
                continue;
            }
            $ip_arr = explode('/', $network);
            $network_long = ip2long($ip_arr[0]);

            $x = ip2long($ip_arr[1]);
            $mask = long2ip($x) == $ip_arr[1] ? $x : 0xffffffff << (32 - $ip_arr[1]);
            $ip_long = ip2long($ip);
            if (($ip_long & $mask) == ($network_long & $mask)) {
                return true;
            }
        }

        return false;
    }
}