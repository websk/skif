<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\EntityService;

/**
 * Class ContentPhotoService
 * @method ContentPhoto getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Content
 */
class ContentPhotoService extends EntityService
{
    /** @var ContentPhotoRepository */
    protected $repository;

    /**
     * @param int $content_id
     * @return array|null
     */
    public function getIdsArrByContentId(int $content_id)
    {
        $ids_arr = $this->repository->findIdsByContentId($content_id);

        return $ids_arr;
    }
}
