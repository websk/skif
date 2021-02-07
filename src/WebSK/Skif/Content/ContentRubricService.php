<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;

/**
 * Class ContentRubricService
 * @method ContentRubric getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Content
 */
class ContentRubricService extends EntityService
{
    const CACHE_KEY_IDS_BY_CONTENT_ID = '\ContentRubricService::getIdsArrByContentId:%d';

    /** @var ContentRubricRepository */
    protected $repository;

    /**
     * @return int
     */
    protected function getCacheTtlSeconds(): int
    {
        return 60 * 60 * 24 * 30 - 1; // 1 месяц
    }

    /**
     * @param InterfaceEntity|ContentRubric $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj)
    {
        $cache_key = sprintf(self::CACHE_KEY_IDS_BY_CONTENT_ID, $entity_obj->getContentId());
        $this->cache_service->delete($cache_key);

        parent::afterSave($entity_obj);
    }

    /**
     * @param InterfaceEntity|ContentRubric $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj)
    {
        $cache_key = sprintf(self::CACHE_KEY_IDS_BY_CONTENT_ID, $entity_obj->getContentId());
        $this->cache_service->delete($cache_key);

        parent::afterDelete($entity_obj);
    }

    /**
     * @param int $rubric_id
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public function getIdsArrByRubricId(int $rubric_id, int $limit_to_page = 0, int $page = 1)
    {
        return $this->repository->findIdsByRubricId($rubric_id, $limit_to_page, $page);
    }

    /**
     * @param int $content_id
     */
    public function deleteByContentId(int $content_id)
    {
        $content_rubrics_ids_arr = $this->getIdsArrByContentId($content_id);

        foreach ($content_rubrics_ids_arr as $content_rubrics_id) {
            $content_rubric_obj = $this->getById($content_rubrics_id);

            $this->delete($content_rubric_obj);
        }
    }

    /**
     * @param int $content_id
     * @return array
     */
    public function getIdsArrByContentId(int $content_id)
    {
        $cache_key = sprintf(self::CACHE_KEY_IDS_BY_CONTENT_ID, $content_id);

        $cached_obj = $this->cache_service->get($cache_key);
        if ($cached_obj !== false) {
            return $cached_obj;
        }

        $ids_arr = $this->repository->findIdsByContentId($content_id);

        $cache_ttl_seconds = $this->getCacheTtlSeconds();

        $this->cache_service->set($cache_key, $ids_arr, $cache_ttl_seconds);

        return $ids_arr;
    }

    /**
     * @param int $content_id
     * @return array
     */
    public function getRubricIdsArrByContentId(int $content_id)
    {
        $content_rubrics_ids_arr = $this->getIdsArrByContentId($content_id);

        $rubric_ids_arr = [];

        foreach ($content_rubrics_ids_arr as $content_rubrics_id) {
            $content_rubrics_obj = $this->getById($content_rubrics_id);

            $rubric_ids_arr[] = $content_rubrics_obj->getRubricId();
        }

        return $rubric_ids_arr;
    }

    /**
     * @param int $content_id
     * @return int
     */
    public function getCountRubricIdsArrByContentId(int $content_id): int
    {
        return count($this->getRubricIdsArrByContentId($content_id));
    }

    /**
     * @param int $rubric_id
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public function getContentIdsArrByRubricId(int $rubric_id, int $limit_to_page = 0, int $page = 1)
    {
        $content_rubrics_ids_arr = $this->getIdsArrByRubricId($rubric_id, $limit_to_page, $page);

        $content_ids_arr = [];

        foreach ($content_rubrics_ids_arr as $content_rubrics_id) {
            $content_rubrics_obj = $this->getById($content_rubrics_id);

            $content_ids_arr[] = $content_rubrics_obj->getContentId();
        }

        return $content_ids_arr;
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
