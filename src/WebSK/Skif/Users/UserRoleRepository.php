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
        $db_table_name = $this->getTableName();
        $db_id_field_name = $this->getIdFieldName();

        return $this->db_service->readColumn(
            'SELECT ' . Sanitize::sanitizeSqlColumnName($db_id_field_name)
            . ' FROM ' . Sanitize::sanitizeSqlColumnName($db_table_name)
            . ' WHERE ' . Sanitize::sanitizeSqlColumnName(UserRole::_USER_ID) . '=?',
            [$user_id]
        );
    }
}
