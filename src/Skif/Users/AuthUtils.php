<?php

namespace Skif\Users;


class AuthUtils
{

    const ROLE_ADMIN = 1;


    /**
     * Авторизация на сайте
     * @param $email
     * @param $password
     * @param $save_auth
     * @return bool|mixed
     */
    public static function doLogin($email, $password, $save_auth = false)
    {
        $salt_password = self::getHash($password);

        $query = "SELECT id FROM " . \Skif\Users\User::DB_TABLE_NAME . " WHERE confirm=1 AND email=? AND passw=? LIMIT 1";
        $user_id = \Skif\DB\DBWrapper::readField($query, array($email, $salt_password));

        if (!$user_id) {
            return false;
        }

        $delta = null;
        if ($save_auth) {
            $delta = time() + 86400 * 30;
        }

        $time = time();
        $session = sha1($email . $user_id . $time);
        $query = "INSERT INTO sessions SET user_id=?, session=?, hostname=?, timestamp=?";
        \Skif\DB\DBWrapper::query($query, array($user_id, $session, $_SERVER['REMOTE_ADDR'], $time));
        setcookie('auth_session', $session, $delta, '/');

        $delta = time() - 86400 * 30;
        $query = "DELETE FROM sessions WHERE user_id=? AND timestamp<=?";
        \Skif\DB\DBWrapper::query($query, array($user_id, $delta));

        return true;
    }

    /**
     * Хеш пароля
     * @param $password
     * @return string
     */
    public static function getHash($password)
    {
        $salt = \Skif\Conf\ConfWrapper::value('salt');

        $hash = md5($salt . $password);

        return $hash;
    }

    /**
     * Выход
     */
    public static function logout()
    {
        $user_id = self::getCurrentUserId();

        if ($user_id) {
            self::clearUserSession($user_id);
        }
        //\Hybrid_Auth::logoutAllProviders();
    }

    public static function clearUserSession($user_id)
    {
        $query = "DELETE FROM sessions WHERE session=?";
        \Skif\DB\DBWrapper::query($query, array($_COOKIE['auth_session']));

        $delta = time() - 86400 * 30;
        $query = "DELETE FROM sessions WHERE user_id=? AND timestamp<=?";
        \Skif\DB\DBWrapper::query($query, array($user_id, $delta));

        self::clearAuthCookie();
        //self::removeUserFromAuthCache($user_id);
    }

    /*
    public static function removeUserFromAuthCache($user_session_id)
    {
        \Skif\Cache\CacheWrapper::delete('auth_user_' . $user_session_id);
    }
    */

    public static function clearAuthCookie()
    {
        setcookie('auth_session', '', time() - 3600, '/');
    }

    /**
     * UserID авторизованного пользователя
     * @return null
     */
    public static function getCurrentUserId()
    {
        static $user_session_unique_id;

        if (isset($user_session_unique_id)) {
            return $user_session_unique_id;
        }

        if (array_key_exists('auth_session', $_COOKIE)) {
            $query = "SELECT user_id FROM sessions WHERE session=?";
            $user_id = \Skif\DB\DBWrapper::readField($query, array($_COOKIE['auth_session']));
            $user_session_unique_id = $user_id;

            return $user_id;
        }

        return null;
    }

    /**
     * @return bool
     */
    public static function currentUserIsAdmin()
    {
        $user_id = self::getCurrentUserId();
        if (!$user_id) {
            return false;
        }

        $user_obj = \Skif\Users\User::factory($user_id, false);
        if (!$user_obj) {
            return false;
        }

        if ($user_obj->hasRoleAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Есть ли у пользователя роль, по обозначению роли
     * @param $role_designation
     * @return bool
     */
    public static function currentUserHasAccessByRoleDesignation($role_designation)
    {
        $user_id = \Skif\Users\AuthUtils::getCurrentUserId();

        if ($user_id) {
            $user_obj = \Skif\Users\User::factory($user_id);

            if ($user_obj->hasRoleByDesignation($role_designation)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $provider_name
     * @param $destination
     * @return \Hybrid_Provider_Adapter|null
     */
    public static function socialLogin($provider_name, $destination)
    {
        $config = self::getConfig();
        $params = array();
        $message = "Неизвестная ошибка авторизации";

        if (!array_key_exists($provider_name, $config['providers'])) {
            self::addFlashMessage($message);
            return null;
        }

        $filtered_destination = filter_var($destination, FILTER_VALIDATE_URL);
        if ($filtered_destination) {
            $params['hauth_return_to'] = \Skif\UrlManager::getUriNoQueryString() . '?destination='
                . $filtered_destination . '&Provider=' . $provider_name;
            //$params['hauth_return_to'] = $filtered_destination;
        }

        //hybridauth use exception for control
        try {
            $hybrid_auth = new \Hybrid_Auth($config);

            $provider = $hybrid_auth->authenticate($provider_name, $params);
            //if user is not logged in hybrid will initialize login process and redirect with die(),
            //so next line will be run only if there is logged in user or any error occurred
            return $provider;
        } catch (\Exception $e) {
            switch ($e->getCode()) {
                case 0 :
                    $message = "Unspecified error.";
                    break;
                case 1 :
                    $message = "Hybriauth configuration error.";
                    break;
                case 2 :
                    $message = "Provider not properly configured.";
                    break;
                case 3 :
                    $message = "Unknown or disabled provider.";
                    break;
                case 4 :
                    $message = "Missing provider application credentials.";
                    break;
                case 5 :
                    $message = "Authentication failed. The user has canceled the authentication or the provider refused the connection.";
                    break;
                case 6 :
                    $message = "Authentication failed. The user has canceled the authentication or the provider refused the connection.";
                    break;

                default:
                    $message = "Unspecified error!";
            }
            self::addFlashMessage($message);
        }

        return null;
    }

    public static function getUserIdIfExistByProvider($provider, $provider_uid)
    {
        $sql = "SELECT id FROM users WHERE provider = ? AND provider_uid = ?";
        $result = \Skif\DB\DBWrapper::readField(
            $sql,
            array($provider, $provider_uid)
        );
        if ($result === false) {
            return false;//no id
        }

        return $result;
    }

    /**
     * @param $user_profile \Hybrid_User_Profile
     * @param $provider
     * @return bool
     */
    public static function registerUserByHybridauthProfile($user_profile, $provider)
    {
        $user_obj = new \Skif\Users\User();

        $user_obj->setProvider($provider);
        $user_obj->setProviderUid($user_profile->identifier);
        $user_obj->setProfileUrl($user_profile->profileURL);
        $user_obj->setName($user_profile->displayName);
        $user_obj->first_name = $user_profile->firstName;
        $user_obj->last_name = $user_profile->lastName;

        // twitter и vkontakte не дают адрес почты
        if ($user_profile->email) {
            $user_obj->setEmail($user_profile->email);
        }

        if (!empty($user_profile->email)) {
            $user_obj->email_verified = ($user_profile->emailVerified === $user_profile->email);
        }

        $user_obj->addRoles(array(\Skif\Auth\User::ROLE_AUTHENTICATED_USER));
        $user_obj->last_modified_at = date('Y-m-d H:i:s');

        if (!empty($user_profile->photoURL)) {
            //save remote image to local
            $user_obj->photo_url = self::saveRemoteUserProfileImage($user_profile->photoURL);
        }

        $user_obj->save();

        if (empty($user_obj->getId())) {
            return false;
        }

        $auth_throttle = new \Skif\Auth\AuthThrottle();
        $auth_throttle->user_id = $user_obj->id;
        $auth_throttle->ip_address = \Skif\Util\Network::get_client_ip();
        $auth_throttle->save();

        if (!$auth_throttle->id) {
            return false;
        }

        return $user_obj->getId();
    }

    public static function saveRemoteUserProfileImage($image_path)
    {
        $image_manager = new \Skif\Image\ImageManager();
        $image_name = $image_manager->storeRemoteImageFile($image_path);
        return $image_name;
    }

    public static function getConfig()
    {
        return \Skif\Conf\ConfWrapper::value('auth.hybrid');
    }
}