<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\EntityService;

/**
 * Class ContentService
 * @method Content getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Content
 */
class ContentService extends EntityService
{
    /** @var ContentRepository */
    protected $repository;

    public function getIdByAlias(string $alias)
    {
        $id = $this->repository->findIdByAlias($alias);

        return $id;
    }
}
