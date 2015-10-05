<?php

namespace Skif\Users;

class User implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    protected $id;
    protected $name;
    protected $birthday;
    protected $phone;
    protected $email;
    protected $city;
    protected $address;
    protected $company;
    protected $comment;
    protected $confirm;
    protected $photo = '';
    protected $passw;
    protected $provider = '';
    protected $provider_uid = '';
    protected $profile_url = '';
    protected $created_at;
    protected $roles_ids_arr;

    public static $active_record_ignore_fields_arr = array(
        'roles_ids_arr'
    );

    const DB_TABLE_NAME = 'users';

    public function load($id)
    {
        $query = "SELECT * FROM users WHERE id=?";
        $raw_obj = \Skif\DB\DBWrapper::readObject($query, array($id));

        if (!$raw_obj) {
            return false;
        }

        $object_vars_arr = get_object_vars($raw_obj);
        foreach ($object_vars_arr as $key => $value) {
            $this->$key = $value;
        }

        $query = "SELECT role_id FROM users_roles WHERE user_id=?";
        $this->roles_ids_arr =  \Skif\DB\DBWrapper::readColumn($query, array($id));

        return true;
    }

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

    public function getRolesIdsArr()
    {
        return $this->roles_ids_arr;
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
     * Является ли пользователь администратором
     * @return bool
     */
    public function hasRoleAdmin()
    {
        if (in_array(\Skif\Users\AuthUtils::ROLE_ADMIN, $this->getRolesIdsArr())) {
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
        $roles_ids_arr = $this->getRolesIdsArr();

        foreach ($roles_ids_arr as $role_id) {
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

    public function save()
    {
        \Skif\Util\ActiveRecordHelper::saveModelObj($this);

        self::removeObjFromCacheById($this->getId());

        \Skif\Logger\Logger::logObjectEvent($this, 'изменение');
    }

    public function delete()
    {
        \Skif\Util\ActiveRecordHelper::deleteModelObj($this);

        self::removeObjFromCacheById($this->getId());

        \Skif\Logger\Logger::logObjectEvent($this, 'удаление');
    }
}