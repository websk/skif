<?php

namespace WebSK\Skif\Auth;

use WebSK\Skif\ConfWrapper;
use Websk\Slim\Container;
use Websk\Skif\DBWrapper;
use WebSK\Skif\Image\ImageManager;
use Websk\Skif\Messages;
use WebSK\Skif\Users\User;
use WebSK\Skif\Users\UserRole;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\Utils\Url;

/**
 * Class AuthUtils
 * @package WebSK\WebSK\Skif\Users
 */
class Auth
{
    const SESSION_LIFE_TIME = 31536000; // 1 год

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

        $query = "SELECT id FROM " . User::DB_TABLE_NAME . " WHERE email=? AND passw=? LIMIT 1";
        $user_id = DBWrapper::readField($query, [$email, $salt_password]);

        if (!$user_id) {
            return false;
        }

        $container = Container::self();
        $user_obj = UsersServiceProvider::getUserService($container)->getById($user_id);

        // Регистрация не подтверждена
        if (!$user_obj->isConfirm()) {
            return false;
        }

        $delta = null;
        if ($save_auth) {
            $delta = time() + self::SESSION_LIFE_TIME;
        }

        $session = sha1(time() . $user_id);

        self::storeUserSession($user_id, $session, $delta);

        return true;
    }

    /**
     * @param int $user_id
     * @param string $session
     * @param int $delta
     * @throws \Exception
     */
    public static function storeUserSession($user_id, $session, $delta)
    {
        $time = time();

        $query = "INSERT INTO sessions SET user_id=?, session=?, hostname=?, timestamp=?";
        DBWrapper::query($query, [$user_id, $session, $_SERVER['REMOTE_ADDR'], $time]);

        setcookie('auth_session', $session, $delta, '/');

        self::clearOldSessionsByUserId($user_id);
    }

    /**
     * Удаляем просроченные сессии
     * @param $user_id
     * @throws \Exception
     */
    protected static function clearOldSessionsByUserId($user_id)
    {
        $delta = time() - self::SESSION_LIFE_TIME;
        $query = "DELETE FROM sessions WHERE user_id=? AND timestamp<=?";
        DBWrapper::query($query, array($user_id, $delta));
    }

    /**
     * Хеш пароля
     * @param $password
     * @return string
     */
    public static function getHash($password)
    {
        $salt = ConfWrapper::value('salt');

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
            $container = Container::self();

            $session_service = UsersServiceProvider::getSessionService($container);

            $session_service->clearUserSession($user_id);
        }
        //\Hybrid_Auth::logoutAllProviders();
    }

    public static function clearUserSession($user_id)
    {
        $query = "DELETE FROM sessions WHERE session=?";
        DBWrapper::query($query, array($_COOKIE['auth_session']));

        self::clearOldSessionsByUserId($user_id);

        self::clearAuthCookie();
    }

    public static function clearAuthCookie()
    {
        setcookie('auth_session', '', time() - 3600, '/');
    }

    /**
     * UserID авторизованного пользователя
     * @return int|null
     */
    public static function getCurrentUserId()
    {
        static $user_session_unique_id;

        if (isset($user_session_unique_id)) {
            return $user_session_unique_id;
        }

        if (array_key_exists('auth_session', $_COOKIE)) {
            $query = "SELECT user_id FROM sessions WHERE session=?";
            $user_id = (int)DBWrapper::readField($query, array($_COOKIE['auth_session']));
            $user_session_unique_id = $user_id;

            return $user_id;
        }

        return null;
    }

    /**
     * @return User
     * @throws \Exception
     */
    public static function getCurrentUserObj()
    {
        $user_id = self::getCurrentUserId();

        $container = Container::self();

        $user_service = UsersServiceProvider::getUserService($container);

        return $user_service->getById($user_id, false);
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

        $container = Container::self();

        $user_service = UsersServiceProvider::getUserService($container);

        $user_obj = $user_service->getById($user_id, false);
        if (!$user_obj) {
            return false;
        }

        if ($user_service->hasRoleAdminByUserId($user_id)) {
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
        $user_id = self::getCurrentUserId();

        if ($user_id) {
            $container = Container::self();

            $user_service = UsersServiceProvider::getUserService($container);

            if ($user_service->hasRoleByUserIdAndDesignation($user_id, $role_designation)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function useSocialLogin()
    {
        $social_config = ConfWrapper::value('auth.hybrid');
        if (!empty($social_config)) {
            return true;
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
        $config = ConfWrapper::value('auth.hybrid');

        $params = array();

        $message = "Неизвестная ошибка авторизации";

        if (!array_key_exists($provider_name, $config['providers'])) {
            Messages::setError($message);
            return null;
        }

        $filtered_destination = filter_var($destination, FILTER_VALIDATE_URL);
        if ($filtered_destination) {
            $params['hauth_return_to'] = Url::getUriNoQueryString() . '?destination='
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
                case 0:
                    $message = "Unspecified error.";
                    break;
                case 1:
                    $message = "Hybriauth configuration error.";
                    break;
                case 2:
                    $message = "Provider not properly configured.";
                    break;
                case 3:
                    $message = "Unknown or disabled provider.";
                    break;
                case 4:
                    $message = "Missing provider application credentials.";
                    break;
                case 5:
                    $message = "Authentication failed. The user has canceled the authentication or the provider refused the connection.";
                    break;
                case 6:
                    $message = "Authentication failed. The user has canceled the authentication or the provider refused the connection.";
                    break;

                default:
                    $message = "Unspecified error!";
            }
            Messages::setError($message);
        }

        return null;
    }

    /**
     * @param $provider_name
     * @param $provider_uid
     * @return bool|int
     */
    public static function getUserIdIfExistByProvider($provider_name, $provider_uid)
    {
        $query = "SELECT id FROM " . User::DB_TABLE_NAME . " WHERE provider = ? AND provider_uid = ?";
        $result = DBWrapper::readField(
            $query,
            [$provider_name, $provider_uid]
        );

        if ($result === false) {
            return false;
        }

        return (int)$result;
    }

    /**
     * @param $user_profile \Hybrid_User_Profile
     * @param $provider
     * @return bool
     */
    public static function registerUserByHybridAuthProfile($user_profile, $provider)
    {
        $user_obj = new User();

        $user_obj->setProvider($provider);
        $user_obj->setProviderUid($user_profile->identifier);
        $user_obj->setProfileUrl($user_profile->profileURL);
        $user_obj->setName($user_profile->displayName);
        $user_obj->setFirstName($user_profile->firstName);
        $user_obj->setLastName($user_profile->lastName);

        // twitter и vkontakte не дают адрес почты
        if ($user_profile->email) {
            $user_obj->setEmail($user_profile->email);
        }

        /*
        if (!empty($user_profile->email)) {
            $user_obj->email_verified = ($user_profile->emailVerified === $user_profile->email);
        }
        */

        if (!empty($user_profile->photoURL)) {
            // save remote image to local
            $photo = self::saveRemoteUserProfileImage($user_profile->photoURL);
            $user_obj->setPhoto($photo);
        }

        $container = Container::self();

        $user_service = UsersServiceProvider::getUserService($container);
        $user_service->save($user_obj);

        if (!$user_obj->getId()) {
            return false;
        }

        // Roles
        $role_id = ConfWrapper::value('user.default_role_id', 0);

        $user_role_obj = new UserRole();
        $user_role_obj->setUserId($user_obj->getId());
        $user_role_obj->setRoleId($role_id);

        $user_role_service = UsersServiceProvider::getUserRoleService($container);
        $user_role_service->save($user_role_obj);

        return $user_obj->getId();
    }

    /**
     * @param string $image_path
     * @return string
     */
    public static function saveRemoteUserProfileImage($image_path)
    {
        $image_manager = new ImageManager();
        $image_name = $image_manager->storeRemoteImageFile($image_path, 'user');

        return $image_name;
    }
}
