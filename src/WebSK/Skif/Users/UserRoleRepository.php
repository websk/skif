<?php

namespace WebSK\Skif\Users;

use WebSK\Entity\BaseEntityRepository;
use WebSK\Utils\Sanitize;

class UserRoleRepository extends BaseEntityRepository
{
    /**
     * @param int $user_id
     * @return array
     * @throws \Exception
     */
    public function findIdsArrForUserId(int $user_id): array
    {
        return $this->db_service->readColumn(
            'SELECT ' . Sanitize::sanitizeSqlColumnName($this->getIdFieldName())
            . ' FROM ' . Sanitize::sanitizeSqlColumnName($this->getTableName())
            . ' WHERE ' . Sanitize::sanitizeSqlColumnName(UserRole::_USER_ID) . '=?',
            [$user_id]
        );
    }
}
