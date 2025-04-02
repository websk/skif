<?php

namespace WebSK\Skif\Blocks;

use WebSK\Entity\EntityRepository;
use WebSK\Utils\Sanitize;

class BlockRoleRepository extends EntityRepository
{
    /**
     * @param int $block_id
     * @return array
     * @throws \Exception
     */
    public function findIdsByBlockId(int $block_id): array
    {
        $query = "SELECT " . Sanitize::sanitizeSqlColumnName($this->getIdFieldName()) .
            " FROM " . Sanitize::sanitizeSqlColumnName($this->getTableName()) .
            " WHERE " . Sanitize::sanitizeSqlColumnName(BlockRole::_BLOCK_ID) . " = ?";

        return $this->db_service->readColumn(
            $query,
            [$block_id]
        );
    }
}