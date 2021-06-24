<?php

namespace WebSK\Skif\Comment;

use WebSK\Entity\Entity;

/**
 * Class Comment
 * @package WebSK\Skif\Comment
 */
class Comment extends Entity
{
    const DB_TABLE_NAME = 'comments';

    const _PARENT_ID = 'parent_id';
    protected ?int $parent_id = null;

    const _COMMENT = 'comment';
    protected string $comment;

    const _URL = 'url';
    protected string $url;

    const _USER_ID = 'user_id';
    protected ?int $user_id = null;

    const _USER_NAME = 'user_name';
    protected ?string $user_name = null;

    const _USER_EMAIL = 'user_email';
    protected ?string $user_email = null;

    const _URL_MD5 = 'url_md5';
    protected string $url_md5;

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
     * @return null|string
     */
    public function getUserName(): ?string
    {
        return $this->user_name;
    }

    /**
     * @param null|string $user_name
     */
    public function setUserName(?string $user_name)
    {
        $this->user_name = $user_name;
    }

    /**
     * @return null|string
     */
    public function getUserEmail(): ?string
    {
        return $this->user_email;
    }

    /**
     * @param null|string $user_email
     */
    public function setUserEmail(?string $user_email)
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
}
