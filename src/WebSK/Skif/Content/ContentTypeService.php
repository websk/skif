<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\EntityService;

/**
 * Class ContentTypeService
 * @method ContentType getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Content
 */
class ContentTypeService extends EntityService
{
    /** @var ContentTypeRepository */
    protected $repository;

    /**
     * @param string $type
     * @return ContentType|null
     */
    public function getByType(string $type)
    {
        $id = $this->repository->findIdByType($type);

        if (!$id) {
            return null;
        }

        return $this->getById($id);
    }
}
