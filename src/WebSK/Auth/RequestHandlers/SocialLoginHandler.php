<?php

namespace WebSK\Auth\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Utils\Messages;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Auth\Auth;
use WebSK\Auth\Users\UsersUtils;

/**
 * Class SocialLoginHandler
 * @package WebSK\Auth\RequestHandlers
 */
class SocialLoginHandler extends BaseHandler
{
    public function __invoke(Request $request, Response $response, string $provider_name)
    {
        $destination = '/';
        if (array_key_exists('destination', $_REQUEST)) {
            $destination = $_REQUEST['destination'];
        }


        $provider = Auth::socialLogin($provider_name, $destination);
        if (!$provider) {
            return $response->withRedirect($destination);
        }

        $is_connected = $provider->isUserConnected();
        if (!$is_connected) {
            Messages::setError("Не удалось соединиться с " . $provider_name);
            return $response->withRedirect($destination);
        }

        /**
         * @var \Hybrid_User_Profile $user_profile
         */
        $user_profile = $provider->getUserProfile();

        $user_id = Auth::getUserIdIfExistByProvider(
            $provider_name,
            $user_profile->identifier
        );

        // Пользователь не найден в базе, регистрируем
        if (!$user_id) {
            if ($user_profile->email) {
                $user_id = UsersUtils::getUserIdByEmail($user_profile->email);

                if ($user_id) {
                    Messages::setError("Пользователь с таким адресом электронной почты " . $user_profile->email . ' уже зарегистрирован');
                    return $response->withRedirect($destination);
                }
            }

            $user_id = Auth::registerUserByHybridAuthProfile(
                $user_profile,
                $provider_name
            );

            if (!$user_id) {
                Messages::setError("Не удалось зарегистрировать нового пользователя");
                return $response->withRedirect($destination);
            }
        }

        $session = sha1(time() . $user_id);
        $delta = time() + Auth::SESSION_LIFE_TIME;

        Auth::storeUserSession($user_id, $session, $delta);

        return $response->withRedirect($destination);
    }
}
