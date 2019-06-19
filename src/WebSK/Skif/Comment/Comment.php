<?php

namespace WebSK\Skif\Comment;

use WebSK\Entity\Entity;
use WebSK\Slim\Container;
use WebSK\Auth\Users\UsersServiceProvider;
use WebSK\Utils\Assert;

/**
 * Class Comment
 * @package WebSK\Skif\Comment
 */
class Comment extends Entity
{
    const ENTITY_SERVICE_CONTAINER_ID = 'skif.comment_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'skif.comment_repository';
    const DB_TABLE_NAME = 'comments';

    const _PARENT_ID = 'parent_id';
    /** @var int */
    protected $parent_id = 0;

    const _COMMENT = 'comment';
    /** @var string */
    protected $comment;

    const _URL = 'url';
    /** @var string */
    protected $url;

    const _USER_ID = 'user_id';
    /** @var int|null */
    protected $user_id = null;

    const _USER_NAME = 'user_name';
    /** @var string */
    protected $user_name;

    const _USER_EMAIL = 'user_email';
    /** @var string */
    protected $user_email;

    const _DATE_TIME = 'date_time';
    /** @var string */
    protected $date_time;

    const _URL_MD5 = 'url_md5';
    /** @var string */
    protected $url_md5;

    /**
     * Parent ID
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    /**
     * @param ?int $parent_id
     */
    public function setParentId(?int $parent_id)
    {
        $this->parent_id = $parent_id;
    }

    /**
     * Page URL
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrlMd5(): string
    {
        return $this->url_md5;
    }

    /**
     * @param string $url_md5
     */
    public function setUrlMd5(string $url_md5)
    {
        $this->url_md5 = $url_md5;
    }

    /**
     * User ID
     * @return null|int
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * @param int|null $user_id
     */
    public function setUserId(?int $user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Имя незарегистрированного пользователя
     * @return string
     */
    public function getUserName(): string
    {
        if ($this->user_id) {
            $container = Container::self();
            $user_service = UsersServiceProvider::getUserService($container);

            $user_obj = $user_service->getById($this->user_id);
            Assert::assert($user_obj);

            return $user_obj->getName();
        }

        return $this->user_name;
    }

    /**
     * @param string $user_name
     */
    public function setUserName(string $user_name)
    {
        $this->user_name = $user_name;
    }

    /**
     * Email незарегистрированного пользователя
     * @return string
     */
    public function getUserEmail(): string
    {
        if ($this->user_id) {
            $container = Container::self();
            $user_service = UsersServiceProvider::getUserService($container);

            $user_obj = $user_service->getById($this->user_id);

            return $user_obj->getEmail();
        }

        return $this->user_email;
    }

    /**
     * @param string $user_email
     */
    public function setUserEmail(string $user_email)
    {
        $this->user_email = $user_email;
    }

    /**
     * Комментарий
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Время в формате unix time
     * @return int
     */
    public function getUnixTime()
    {
        return strtotime($this->date_time);
    }

    /**
     * @return mixed
     */
    public function getDateTime()
    {
        return $this->date_time;
    }

    /**
     * @param mixed $date_time
     */
    public function setDateTime($date_time)
    {
        $this->date_time = $date_time;
    }
}
