<?php

namespace WebSK\Skif\Users\RequestHandlers;

use Skif\Image\ImageConstants;
use Skif\Image\ImageController;
use Slim\Http\Request;
use Slim\Http\Response;
use Websk\Skif\Messages;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\Skif\Users\AuthUtils;
use WebSK\Skif\Users\User;
use WebSK\Skif\Users\UserRole;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\Skif\Users\UsersUtils;
use WebSK\Utils\HTTP;

/**
 * Class UserSaveHandler
 * @package WebSK\Skif\Users\RequestHandlers
 */
class UserSaveHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int|null $user_id
     * @return \Psr\Http\Message\ResponseInterface|Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, ?int $user_id)
    {
        $user_service = UsersServiceProvider::getUserService($this->container);

        if ($user_id != 'new') {
            $current_user_id = AuthUtils::getCurrentUserId();

            if (($current_user_id != $user_id) && !AuthUtils::currentUserIsAdmin()) {
                return $response->withStatus(HTTP::STATUS_FORBIDDEN);
            }

            $user_obj = $user_service->getById($user_id, false);

            if (!$user_obj) {
                return $response->withStatus(HTTP::STATUS_NOT_FOUND);
            }
        } else {
            $user_obj = new User();
        }

        $destination = array_key_exists('destination', $_REQUEST) ? $_REQUEST['destination'] : '/user/edit/' . $user_id;

        $name = array_key_exists('name', $_REQUEST) ? $_REQUEST['name'] : '';
        $first_name = array_key_exists('first_name', $_REQUEST) ? $_REQUEST['first_name'] : '';
        $last_name = array_key_exists('last_name', $_REQUEST) ? $_REQUEST['last_name'] : '';
        $roles_ids_arr = array_key_exists('roles', $_REQUEST) ? $_REQUEST['roles'] : null;
        $confirm = array_key_exists('confirm', $_REQUEST) ? $_REQUEST['confirm'] : false;
        $birthday = array_key_exists('birthday', $_REQUEST) ? $_REQUEST['birthday'] : '';
        $email = array_key_exists('email', $_REQUEST) ? $_REQUEST['email'] : '';
        $phone = array_key_exists('phone', $_REQUEST) ? $_REQUEST['phone'] : '';
        $city = array_key_exists('city', $_REQUEST) ? $_REQUEST['city'] : '';
        $address = array_key_exists('address', $_REQUEST) ? $_REQUEST['address'] : '';
        $comment = array_key_exists('comment', $_REQUEST) ? $_REQUEST['comment'] : '';
        $new_password_first = array_key_exists('new_password_first', $_REQUEST) ? $_REQUEST['new_password_first'] : '';
        $new_password_second = array_key_exists('new_password_second', $_REQUEST) ? $_REQUEST['new_password_second'] : '';

        if (empty($email)) {
            Messages::setError('Ошибка! Не указан Email.');
            return $response->withRedirect($destination);
        }

        if (empty($name)) {
            Messages::setError('Ошибка! Не указаны Фамилия Имя Отчество.');
            return $response->withRedirect($destination);
        }

        /*
        if (!\WebSK\Skif\Users\UsersUtils::checkBirthDay::checkBirthDay($birthday)) {
            \Websk\Skif\Messages::setError('Указана неверная дата рождения');
            \Skif\Http::redirect($destination);
        }
        */

        if ($user_id == 'new') {
            $has_user_id = UsersUtils::hasUserByEmail($email);
            if ($has_user_id) {
                Messages::setError('Ошибка! Пользователь с таким адресом электронной почты ' . $email . ' уже существует.');
                return $response->withRedirect($destination);
            }

            if (!$new_password_first && !$new_password_second) {
                Messages::setError('Ошибка! Не введен пароль.');
                return $response->withRedirect($destination);
            }
        } else {
            $has_user_id = UsersUtils::hasUserByEmail($email, $user_id);
            if ($has_user_id) {
                Messages::setError('Ошибка! Пользователь с таким адресом электронной почты ' . $email . ' уже существует.');
                return $response->withRedirect($destination);
            }
        }

        // Пароль
        if ($new_password_first || $new_password_second) {
            if ($new_password_first != $new_password_second) {
                Messages::setError('Ошибка! Пароль не подтвержден, либо подтвержден неверно.');
                return $response->withRedirect($destination);
            }

            $user_obj->setPassw(AuthUtils::getHash($new_password_first));
        }

        if (AuthUtils::currentUserIsAdmin()) {
            $user_obj->setConfirm($confirm);
        }

        $user_obj->setName($name);
        $user_obj->setFirstName($first_name);
        $user_obj->setLastName($last_name);
        $user_obj->setBirthday($birthday);
        $user_obj->setPhone($phone);
        $user_obj->setEmail($email);
        $user_obj->setCity($city);
        $user_obj->setAddress($address);
        $user_obj->setComment($comment);

        $user_service->save($user_obj);


        // Roles
        // TODO: убрать
        if (AuthUtils::currentUserIsAdmin()) {
            $user_service->deleteUserRolesForUserId($user_id);

            if ($roles_ids_arr) {
                $user_role_service = UsersServiceProvider::getUserRoleService($this->container);

                foreach ($roles_ids_arr as $role_id) {
                    $user_role_obj = new UserRole();
                    $user_role_obj->setUserId($user_obj->getId());
                    $user_role_obj->setRoleId($role_id);
                    $user_role_service->save($user_role_obj);
                }
            }
        }

        // Image
        if (array_key_exists('image_file', $_FILES)) {
            $file = $_FILES['image_file'];
            if (array_key_exists('name', $file) && !empty($file['name'])) {
                $root_images_folder = ImageConstants::IMG_ROOT_FOLDER;
                $file_name = ImageController::processUpload($file, 'user', $root_images_folder);
                if (!$file_name) {
                    Messages::setError('Не удалось загрузить фотографию.');
                    return $response->withRedirect('/user/edit/' . $user_obj->getId());
                }

                $user_obj = $user_service->getById($user_id);
                $user_obj->setPhoto($file_name);
                $user_service->save($user_obj);
            }
        }

        Messages::setMessage('Информация о пользователе была успешно сохранена');

        $destination = str_replace('/new', '/' . $user_obj->getId(), $destination);

        return $response->withRedirect($destination);
    }
}
