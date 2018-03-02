<?php

namespace Skif\Users;

use Skif\Model\FactoryTrait;
use Skif\Model\InterfaceDelete;
use Skif\Model\InterfaceFactory;
use Skif\Model\InterfaceLoad;
use Skif\Model\InterfaceSave;
use Skif\Util\ActiveRecord;

class UserRole implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete
{
    use ActiveRecord;
    use FactoryTrait;

    const DB_TABLE_NAME = 'users_roles';

    /** @var int */
    protected $id;
    /** @var int */
    protected $user_id;
    /** @var int */
    protected $role_id;

    // Зависит от модели
    public static $depends_on_models_arr = [
        User::class => [
            'link_field' => 'user_id',
        ],
    ];

    /**
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return int
     */
    public function getRoleId()
    {
        return (int)$this->role_id;
    }

    /**
     * @param int $role_id
     */
    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;
    }
}
