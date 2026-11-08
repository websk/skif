<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\EntityService;

/**
 * Class ContentTypeService
 * @method ($exception_if_not_loaded is true ? ContentType : ContentType|null) getById(?int $entity_id, bool $exception_if_not_loaded = true)
 * @package WebSK\Skif\Content
 */
class ContentTypeService extends EntityService
{
    /** @var ContentTypeRepository */
    protected \WebSK\Entity\EntityRepository $repository;

    /**
     * @param string $type
     * @return ContentType|null
     */
    public function getByType(string $type): ?ContentType
    {
        $id = $this->repository->findIdByType($type);

        if (!$id) {
            return null;
        }

        return $this->getById($id);
    }
}
