<?php

namespace Skif\Users;

use Skif\Conf\ConfWrapper;
use Skif\Image\ImageConstants;
use Skif\Image\ImageManager;
use Skif\Model\FactoryTrait;
use Skif\Model\InterfaceDelete;
use Skif\Model\InterfaceFactory;
use Skif\Model\InterfaceLoad;
use Skif\Model\InterfaceLogger;
use Skif\Model\InterfaceSave;
use Skif\Util\ActiveRecord;

class User implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete,
    InterfaceLogger
{
    use ActiveRecord;
    use FactoryTrait;

    /** @var int */
    protected $id;
    /** @var string */
    protected $name;
    /** @var string */
    protected $first_name;
    /** @var string */
    protected $last_name;
    protected $birthday;
    /** @var string */
    protected $phone;
    /** @var string */
    protected $email;
    /** @var string */
    protected $city;
    /** @var string */
    protected $address;
    /** @var string */
    protected $company;
    /** @var string */
    protected $comment;
    /** @var int */
    protected $confirm;
    protected $confirm_code;
    /**
     * @var string
     */
    protected $photo = '';
    /** @var string */
    protected $passw;
    /** @var string */
    protected $provider = '';
    /** @var string */
    protected $provider_uid = '';
    /** @var string */
    protected $profile_url = '';
    protected $created_at;
    /** @var array */
    protected $user_role_ids_arr = [];

    public static $active_record_ignore_fields_arr = [
        'user_role_ids_arr'
    ];

    const DB_TABLE_NAME = 'users';

    // Связанные модели
    public static $related_models_arr = [
        UserRole::class => [
            'link_field' => 'user_id',
            'field_name' => 'user_role_ids_arr',
            'list_title' => 'Роли',
        ],
    ];

    /**
     * ID
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
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
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
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
     * @param string $birthday
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
     * @param string $phone
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
     * @param string $email
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
     * @param string $city
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
     * @param string $address
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
     * @param string $comment
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
     * @param string $photo
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

        $this->setPhoto('');
        $this->save();

        $file_path = ConfWrapper::value('site_path') . '/' . ImageConstants::IMG_ROOT_FOLDER . '/' . $this->getPhotoPath();
        if (!file_exists($file_path)) {
            return false;
        }

        $image_manager = new ImageManager();
        $image_manager->removeImageFile($this->getPhotoPath());

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

    /**
     * @return array
     */
    public function getUserRoleIdsArr()
    {
        return $this->user_role_ids_arr;
    }

    /**
     * @return array
     */
    public function getRoleIdsArr()
    {
        $user_roles_ids_arr = $this->getUserRoleIdsArr();

        $role_ids_arr = [];

        foreach ($user_roles_ids_arr as $user_role_id) {
            $user_role_obj = UserRole::factory($user_role_id);

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
     * @param int $confirm
     */
    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;
    }

    /**
     * @return string
     */
    public function getConfirmCode()
    {
        return $this->confirm_code;
    }

    /**
     * @param string $confirm_code
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
        if (in_array(AuthUtils::ROLE_ADMIN, $this->getRoleIdsArr())) {
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
            
            $role_obj = Role::factory($role_id);

            if (trim($role_obj->getDesignation()) == trim($designation)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getPassw()
    {
        return $this->passw;
    }

    /**
     * @param string $passw
     */
    public function setPassw($passw)
    {
        $this->passw = $passw;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return string
     */
    public function getProviderUid()
    {
        return $this->provider_uid;
    }

    /**
     * @param string $provider_uid
     */
    public function setProviderUid($provider_uid)
    {
        $this->provider_uid = $provider_uid;
    }

    /**
     * @return string
     */
    public function getProfileUrl()
    {
        return $this->profile_url;
    }

    /**
     * @param string $profile_url
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
            $user_role_obj = UserRole::factory($user_role_id);

            $user_role_obj->delete();
        }
    }

    public function afterDelete()
    {
        $this->deletePhoto();
        $this->deleteUserRoles();

        self::removeObjFromCacheById($this->getId());
    }
}