<?php

namespace WebSK\Skif\Users;

use WebSK\Skif\Auth\Auth;
use WebSK\Slim\ConfWrapper;
use Websk\Slim\Container;
use Websk\Skif\DBWrapper;
use WebSK\Utils\Filters;

/**
 * Class UsersUtils
 * @package WebSK\Skif\Users
 */
class UsersUtils
{

    /**
     * @param int $user_id
     * @param bool $exception_if_not_loaded
     * @return User
     * @deprecated
     * @throws \Exception
     */
    public static function loadUser(int $user_id, bool $exception_if_not_loaded = true)
    {
        $container = Container::self();

        $user_service = UsersServiceProvider::getUserService($container);

        return $user_service->getById($user_id, $exception_if_not_loaded);
    }

    /**
     * @param int $role_id
     * @param bool $exception_if_not_loaded
     * @return Role
     * @deprecated
     * @throws \Exception
     */
    public static function loadRole(int $role_id, bool $exception_if_not_loaded = true)
    {
        $container = Container::self();

        $role_service = UsersServiceProvider::getRoleService($container);

        return $role_service->getById($role_id, $exception_if_not_loaded);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public static function getRolesIdsArr()
    {
        $query = "SELECT id FROM " . Role::DB_TABLE_NAME . " ORDER BY name";

        return DBWrapper::readColumn($query);
    }

    /**
     * @param int|null $role_id
     * @return array
     */
    public static function getUsersIdsArr($role_id = null)
    {
        $param_arr = [];

        $query = "SELECT u.id FROM " . User::DB_TABLE_NAME . " u";
        if ($role_id) {
            $query .= " JOIN users_roles ur ON (ur.user_id=u.id) WHERE ur.role_id=?";
            $param_arr[] = $role_id;
        }
        $query .= " ORDER BY u.name";

        return DBWrapper::readColumn($query, $param_arr);
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
     * Генератор пароля
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

    /**
     * ID пользователя по его email
     * @param $email
     * @param null $current_user_id
     * @return mixed
     */
    public static function getUserIdByEmail($email, $current_user_id = null)
    {
        $query = "SELECT id FROM " . User::DB_TABLE_NAME . " WHERE email=?";
        $param_arr = array($email);

        if ($current_user_id) {
            $query .= " AND id!=?";
            $param_arr[] = $current_user_id;
        }

        $query .= " LIMIT 1";

        return DBWrapper::readField($query, $param_arr);
    }

    /**
     * Проверка существования пользователя по его email
     * @param $email
     * @param null $current_user_id
     * @return bool
     */
    public static function hasUserByEmail($email, $current_user_id = null)
    {
        $has_user_id = self::getUserIdByEmail($email, $current_user_id);
        if ($has_user_id) {
            return true;
        }

        return false;
    }

    /**
     * Генератор кода подтверждения регистрации на сайте
     * @return string
     */
    public static function generateConfirmCode()
    {
        $salt = ConfWrapper::value('salt');
        $salt .= $salt;

        $confirm_code = md5($salt . time() . uniqid());

        return $confirm_code;
    }

    /**
     * ID пользователя по коду подтверждения регистрации на сайте
     * @param $confirm_code
     * @return mixed
     */
    public static function getUserIdByConfirmCode($confirm_code)
    {
        $query = "SELECT id FROM " . User::DB_TABLE_NAME . " WHERE confirm_code=? LIMIT 1";

        return DBWrapper::readField($query, array($confirm_code));
    }

    /**
     * Смена и отправка пароля пользователю
     * @param $user_id
     * @return string
     */
    public static function createAndSendPasswordToUser($user_id)
    {
        $new_password = UsersUtils::generatePassword(8);

        $container = Container::self();

        $user_service = UsersServiceProvider::getUserService($container);

        $user_obj = $user_service->getById($user_id);
        $user_obj->setPassw(Auth::getHash($new_password));
        $user_service->save($user_obj);

        if ($user_obj->getEmail()) {
            $site_email = ConfWrapper::value('site_email');
            $site_domain = ConfWrapper::value('site_domain');
            $site_name = ConfWrapper::value('site_name');

            $mail_message = "<p>Добрый день, " . $user_obj->getName() . "</p>";
            $mail_message .= "<p>Вы воспользовались формой восстановления пароля на сайте " . $site_name . "</p>";
            $mail_message .= "<p>Ваш новый пароль: " . $new_password . "<br>";
            $mail_message .= "Ваш email для входа: " . $user_obj->getEmail() . "</p>";
            $mail_message .= "<p>Рекомендуем сменить пароль после входа на сайт.</p>";
            $mail_message .= '<p>' . $site_domain . "</p>";

            $subject = "Смена пароля на сайте " . ConfWrapper::value('site_name');

            $mail = new \PHPMailer;
            $mail->CharSet = "utf-8";
            $mail->setFrom($site_email, $site_name);
            $mail->addAddress($user_obj->getEmail());
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $mail_message;
            $mail->AltBody = Filters::checkPlain($mail_message);
            $mail->send();
        }

        return $new_password;
    }
}
