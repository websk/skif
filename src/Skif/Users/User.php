<?php

namespace Skif\Users;

class User implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete,
    \Skif\Model\InterfaceLogger
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    protected $id;
    protected $name;
    protected $first_name;
    protected $last_name;
    protected $birthday;
    protected $phone;
    protected $email;
    protected $city;
    protected $address;
    protected $company;
    protected $comment;
    protected $confirm;
    protected $confirm_code;
    protected $photo = '';
    protected $passw;
    protected $provider = '';
    protected $provider_uid = '';
    protected $profile_url = '';
    protected $created_at;
    protected $user_role_ids_arr;

    public static $active_record_ignore_fields_arr = array(
        'user_role_ids_arr'
    );

    const DB_TABLE_NAME = 'users';

    // Связанные модели
    public static $related_models_arr = array(
        '\Skif\Users\UserRole' => array(
            'link_field' => 'user_id',
            'field_name' => 'user_role_ids_arr',
            'list_title' => 'Роли',
        ),
    );

    /**
     * ID
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * Имя пользователя
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * Дата рождения
     * @return string
     */
    public function getBirthDay()
    {
        return $this->birthday;
    }

    /**
     * @param mixed $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * Телефон
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Email
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Город
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Адрес
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Комментарий
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Фото
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    public function deletePhoto()
    {
        if (!$this->getPhotoPath()) {
            return true;
        }

        $file_path = \Skif\Conf\ConfWrapper::value('site_path') . '/' . \Skif\Image\ImageConstants::IMG_ROOT_FOLDER . '/' . $this->getPhotoPath();

        if (!file_exists($file_path)) {
            return false;
        }

        $image_manager = new \Skif\Image\ImageManager();
        $image_manager->removeImageFile($this->getPhotoPath());

        $this->setPhoto('');
        $this->save();

        return true;
    }

    /**
     * Путь к фото
     * @return string
     */
    public function getPhotoPath()
    {
        if (!$this->getPhoto()) {
            return '';
        }

        return 'user/'. $this->getPhoto();
    }

    public function getUserRoleIdsArr()
    {
        return $this->user_role_ids_arr;
    }

    public function getRoleIdsArr()
    {
        $user_roles_ids_arr = $this->getUserRoleIdsArr();

        $role_ids_arr = array();

        foreach ($user_roles_ids_arr as $user_role_id) {
            $user_role_obj = \Skif\Users\UserRole::factory($user_role_id);

            $role_ids_arr[] = $user_role_obj->getRoleId();
        }

        return $role_ids_arr;
    }

    /**
     * Регистрация пользователя подтверждена
     * @return bool
     */
    public function isConfirm()
    {
        if ($this->confirm) {
            return true;
        }

        return false;
    }

    /**
     * @param mixed $confirm
     */
    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;
    }

    /**
     * @return mixed
     */
    public function getConfirmCode()
    {
        return $this->confirm_code;
    }

    /**
     * @param mixed $confirm_code
     */
    public function setConfirmCode($confirm_code)
    {
        $this->confirm_code = $confirm_code;
    }

    /**
     * Является ли пользователь администратором
     * @return bool
     */
    public function hasRoleAdmin()
    {
        if (in_array(\Skif\Users\AuthUtils::ROLE_ADMIN, $this->getRoleIdsArr())) {
            return true;
        }

        return false;
    }

    /**
     * Есть ли у пользователя роль, по обозначению роли
     * @param $designation
     * @return bool
     */
    public function hasRoleByDesignation($designation)
    {
        $roles_ids_arr = $this->getRoleIdsArr();

        foreach ($roles_ids_arr as $role_id) {
            if (!$role_id) {
                continue;
            }
            
            $role_obj = \Skif\Users\Role::factory($role_id);

            if (trim($role_obj->getDesignation()) == trim($designation)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getPassw()
    {
        return $this->passw;
    }

    /**
     * @param mixed $passw
     */
    public function setPassw($passw)
    {
        $this->passw = $passw;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param mixed $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return mixed
     */
    public function getProviderUid()
    {
        return $this->provider_uid;
    }

    /**
     * @param mixed $provider_uid
     */
    public function setProviderUid($provider_uid)
    {
        $this->provider_uid = $provider_uid;
    }

    /**
     * @return mixed
     */
    public function getProfileUrl()
    {
        return $this->profile_url;
    }

    /**
     * @param mixed $profile_url
     */
    public function setProfileUrl($profile_url)
    {
        $this->profile_url = $profile_url;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function deleteUserRoles()
    {
        $user_roles_ids_arr = $this->getUserRoleIdsArr();

        foreach ($user_roles_ids_arr as $user_role_id) {
            $user_role_obj = \Skif\Users\UserRole::factory($user_role_id);

            $user_role_obj->delete();
        }
    }

    public function afterDelete()
    {
        $this->deletePhoto();
        $this->deleteUserRoles();

        self::removeObjFromCacheById($this->getId());

        \Skif\Logger\Logger::logObjectEvent($this, 'удаление');
    }
}