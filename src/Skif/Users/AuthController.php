<?php

namespace Skif\Users;


class AuthController
{
    /**
     * Проверка авторизации
     */
    public static function loginAction()
    {
        if (array_key_exists('email', $_REQUEST) && array_key_exists('password', $_REQUEST)) {
            $save_auth = array_key_exists('save_auth', $_REQUEST) ? true : false;
            \Skif\Users\AuthUtils::doLogin($_REQUEST['email'], $_REQUEST['password'], $save_auth);

            $redirect = '/';
            if (isset($_REQUEST['destination'])) {
                $redirect = $_REQUEST['destination'];
            }

            \Skif\Http::redirect($redirect);
        }
    }

    public function logoutAction()
    {
        \Skif\Users\AuthUtils::logout();

        $redirect = '/';
        if (isset($_REQUEST['destination'])) {
            $redirect = $_REQUEST['destination'];
        }

        \Skif\Http::redirect($redirect);
    }

    /*
    public function sessionAction()
    {
        \Skif\CRUDUtils::sendJsonHeaders();

        $current_user_obj = \Skif\Auth\AuthHelper::getCurrentUser();
        if (!$current_user_obj) {
            return '{}';
        }

        echo json_encode($current_user_obj);
        return;
    }

    public function socialAuthAction()
    {
        $params = $_REQUEST;
        if (isset($params['Provider'])) {
            $destination = $params['destination'];//check is url
            $provider = \Skif\Auth\AuthHelper::socialLogin($params['Provider'], $destination);
            if (!$provider) {
                \Skif\CRUDUtils::redirect($destination);
            }

            $is_connected = $provider->isUserConnected();
            if (!$is_connected) {
                \Skif\Auth\AuthHelper::addFlashMessage("Not connected to " . $params['Provider']);
                \Skif\CRUDUtils::redirect($destination);
            }
            /**
             * @var \Hybrid_User_Profile $user_profile
             */
    /*
            $user_profile = $provider->getUserProfile();

            $auth_user_id = \Skif\Auth\AuthHelper::getUserIdIfExistByProvider(
                $params['Provider'],
                $user_profile->identifier
            );

            //no such user in our db, register
            if (!$auth_user_id) {
                $auth_user_id = \Skif\Auth\AuthHelper::registerUserByHybridauthProfile(
                    $user_profile,
                    $params['Provider']
                );

                //some error during save
                if (!$auth_user_id) {
                    \Skif\Auth\AuthHelper::addFlashMessage("Can't create user");
                    \Skif\CRUDUtils::redirect($destination);
                }
            }

            \Skif\Auth\AuthHelper::storeUserSession($auth_user_id, session_id());

            \Skif\CRUDUtils::redirect($destination);
        }
    }

    public function gateAction()
    {
        \Hybrid_Endpoint::process();
    }
    */

}