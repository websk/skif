<?php

namespace WebSK\Skif\Users;

use WebSK\Entity\BaseEntity;

/**
 * Class Role
 * @package WebSK\WebSK\Skif\Users
 */
class Role extends BaseEntity
{
    const ENTITY_SERVICE_CONTAINER_ID = 'users.role_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'users.role_repository';
    const DB_TABLE_NAME = 'roles';

    const ROLE_ADMIN = 1;

    /** @var string */
    protected $name;

    /** @var string */
    protected $designation;

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
    public function getDesignation(): string
    {
        return $this->designation;
    }

    /**
     * @param string $designation
     */
    public function setDesignation(string $designation): void
    {
        $this->designation = $designation;
    }
}
