<?php

namespace WebSK\Skif\Users;

use WebSK\Entity\BaseEntityService;

/**
 * Class UserRoleService
 * @method UserRole getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Users
 */
class UserRoleService extends BaseEntityService
{
    /** @var UserRoleRepository */
    protected $repository;

    /**
     * @param int $user_id
     * @return array
     * @throws \Exception
     */
    public function getIdsArrByUserId(int $user_id)
    {
        return $this->repository->findIdsArrForUserId($user_id);
    }
}
