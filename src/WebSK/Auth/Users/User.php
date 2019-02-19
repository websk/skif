<?php

namespace WebSK\Auth\Users;

use WebSK\Entity\Entity;

/**
 * Class User
 * @package WebSK\Auth\Users
 */
class User extends Entity
{
    const ENTITY_SERVICE_CONTAINER_ID = 'users.user_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'users.user_repository';
    const DB_TABLE_NAME = 'users';

    /** @var string */
    protected $name = '';

    /** @var string */
    protected $first_name = '';

    /** @var string */
    protected $last_name = '';

    /** @var string */
    protected $birthday = '';

    /** @var string */
    protected $phone = '';

    /** @var string */
    protected $email = '';

    /** @var string */
    protected $city = '';

    /** @var string */
    protected $address = '';

    /** @var string */
    protected $company = '';

    /** @var string */
    protected $comment = '';

    /** @var int */
    protected $confirm = false;

    /** @var string */
    protected $confirm_code;

    /** @var string */
    protected $photo = '';

    /** @var string */
    protected $passw;

    /** @var string */
    protected $provider = '';

    /** @var string */
    protected $provider_uid = '';

    /** @var string */
    protected $profile_url = '';

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     */
    public function setFirstName(string $first_name): void
    {
        $this->first_name = $first_name;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     */
    public function setLastName(string $last_name): void
    {
        $this->last_name = $last_name;
    }

    /**
     * @return string
     */
    public function getBirthday(): string
    {
        return $this->birthday;
    }

    /**
     * @param string $birthday
     */
    public function setBirthday(string $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getPhoto(): string
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     */
    public function setPhoto(string $photo): void
    {
        $this->photo = $photo;
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
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getPassw(): string
    {
        return $this->passw;
    }

    /**
     * @param string $passw
     */
    public function setPassw(string $passw): void
    {
        $this->passw = $passw;
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     */
    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    /**
     * @return string
     */
    public function getProviderUid(): string
    {
        return $this->provider_uid;
    }

    /**
     * @param string $provider_uid
     */
    public function setProviderUid(string $provider_uid): void
    {
        $this->provider_uid = $provider_uid;
    }

    /**
     * @return string
     */
    public function getProfileUrl(): string
    {
        return $this->profile_url;
    }

    /**
     * @param string $profile_url
     */
    public function setProfileUrl(string $profile_url): void
    {
        $this->profile_url = $profile_url;
    }

    /**
     * Регистрация пользователя подтверждена
     * @return bool
     */
    public function isConfirm(): bool
    {
        return $this->confirm;
    }

    /**
     * @param bool $confirm
     */
    public function setConfirm(bool $confirm)
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
    public function setConfirmCode(string $confirm_code)
    {
        $this->confirm_code = $confirm_code;
    }
}
