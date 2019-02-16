<?php

namespace WebSK\Logger\Entry;

use WebSK\Entity\Entity;
use WebSK\Entity\ProtectPropertiesTrait;

class LoggerEntry extends Entity
{
    use ProtectPropertiesTrait;

    const ENTITY_SERVICE_CONTAINER_ID = 'logger.entry_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'logger.entry_repository';
    const DB_TABLE_NAME = 'logger_entry';

    const _USER_FULLID = 'user_full_id';
    const _OBJECT_FULLID = 'object_full_id';
    const _SERIALIZED_OBJECT = 'serialized_object';

    /** @var string */
    protected $user_full_id;
    /** @var string */
    protected $object_full_id;
    /** @var string */
    protected $serialized_object;
    /** @var string */
    protected $user_ip;
    /** @var string */
    protected $comment;

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $value
     */
    public function setComment(string $value)
    {
        $this->comment = $value;
    }

    /**
     * @return string
     */
    public function getUserIp(): string
    {
        return $this->user_ip;
    }

    /**
     * @param string $value
     */
    public function setUserIp(string $value)
    {
        $this->user_ip = $value;
    }

    /**
     * @return string
     */
    public function getSerializedObject(): string
    {
        return $this->serialized_object;
    }

    /**
     * @param string $value
     */
    public function setSerializedObject(string $value)
    {
        $this->serialized_object = $value;
    }

    /**
     * @return string
     */
    public function getObjectFullId(): string
    {
        return $this->object_full_id;
    }

    /**
     * @param string $value
     */
    public function setObjectFullId(string $value)
    {
        $this->object_full_id = $value;
    }

    /**
     * @return string
     */
    public function getUserFullId(): ?string
    {
        return $this->user_full_id;
    }

    /**
     * @param null|string $value
     */
    public function setUserFullId(?string $value)
    {
        $this->user_full_id = $value;
    }
}
