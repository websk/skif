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

    /** @var array */
    protected $ids_by_urls_cache = [];

    /**
     * @param string $alias
     * @return int
     */
    public function getIdByAlias(string $alias)
    {
        if (isset($this->ids_by_urls_cache[$alias])) {
            return $this->ids_by_urls_cache[$alias];
        }

        $id = $this->repository->findIdByAlias($alias);

        $this->ids_by_urls_cache[$alias] = $id;

        return $id;
    }
}
