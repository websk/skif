<?php

namespace Skif\Users;


class UsersUtils
{
    public static function getRolesIdsArr()
    {
        $query = "SELECT id FROM roles ORDER BY name";
        return \Skif\DB\DBWrapper::readColumn($query);
    }

    public static function getUsersIdsArr($role_id = null)
    {
        $param_arr = array();

        $query = "SELECT u.id FROM users u";
        if ($role_id) {
            $query .= " JOIN users_roles ur ON (ur.user_id=u.id) WHERE ur.role_id=?";
            $param_arr[] = $role_id;
        }
        $query .= " ORDER BY u.name";

        return \Skif\DB\DBWrapper::readColumn($query, $param_arr);
    }

    /**
     * Проверка даты рождения
     * @param $birthday
     * @return bool
     */
    public static function checkBirthDay($birthday)
    {
        $day = substr($birthday, 0, 2);
        $mon = substr($birthday, 3, 2);
        $year = substr($birthday, 6, 10);

        if ((substr($birthday, 2, 1) == '.') && (substr($birthday, 5, 1) == '.')) {
            if (($day >= 1) && ($day <= 31) && ($mon >= 1) && ($mon <= 12) && ($year >= 1900) && ($year <= date('Y'))) {
                if (is_numeric($day) and is_numeric($mon) and is_numeric($year)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Генерация пароля
     * @param $number
     * @return string
     */
    public static function generatePassword($number)
    {
        $arr = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'v', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'X', 'Y', 'Z',
            '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');

        $pass = '';
        for ($i = 0; $i < $number; $i++) {
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }

        return $pass;
    }

}