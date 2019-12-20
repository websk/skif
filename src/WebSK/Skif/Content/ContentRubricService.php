<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\EntityService;

/**
 * Class ContentRubricService
 * @method ContentRubric getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Content
 */
class ContentRubricService extends EntityService
{
    /** @var ContentRubricRepository */
    protected $repository;

    /**
     * @param int $rubric_id
     * @return array
     */
    public function getIdsArrByRubricId(int $rubric_id)
    {
        return $this->repository->findIdsByRubricId($rubric_id);
    }

    /**
     * @param int $rubric_id
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public function getContentIdsArrByRubricId(int $rubric_id, int $limit_to_page = 0, int $page = 1)
    {
        return $this->repository->findContentIdsByRubricId($rubric_id, $limit_to_page, $page);
    }

    /**
     * @param int $rubric_id
     * @return int
     */
    public function getCountContentsByRubricId(int $rubric_id)
    {
        $contents_ids_arr = $this->getContentIdsArrByRubricId($rubric_id);

        $count_contents = count($contents_ids_arr);

        return $count_contents;
    }

    /**
     * @param int $rubric_id
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public function getPublishedContentIdsArrByRubricId(int $rubric_id, int $limit_to_page = 0, int $page = 1)
    {
        return $this->repository->findPublishedContentIdsArrByRubricId($rubric_id, $limit_to_page, $page);
    }

    /**
     * @param int $rubric_id
     * @return int
     */
    public function getCountPublishedContentsByRubricId(int $rubric_id)
    {
        $contents_ids_arr = $this->getPublishedContentIdsArrByRubricId($rubric_id);

        $count_contents = count($contents_ids_arr);

        return $count_contents;
    }
}
