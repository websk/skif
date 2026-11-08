<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\EntityService;

/**
 * Class ContentPhotoService
 * @method ($exception_if_not_loaded is true ? ContentPhoto : ContentPhoto|null) getById(?int $entity_id, bool $exception_if_not_loaded = true)
 * @package WebSK\Skif\Content
 */
class ContentPhotoService extends EntityService
{
    /** @var ContentPhotoRepository */
    protected \WebSK\Entity\EntityRepository $repository;

    /**
     * @param int $content_id
     * @return list<int>
     */
    public function getIdsArrByContentId(int $content_id): array
    {
        return $this->repository->findIdsByContentId($content_id);
    }
}
