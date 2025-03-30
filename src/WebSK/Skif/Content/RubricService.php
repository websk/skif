<?php

namespace WebSK\Skif\Content;

use WebSK\Cache\CacheService;
use WebSK\Entity\EntityRepository;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Skif\UniqueUrl;
use WebSK\Utils\Assert;
use WebSK\Utils\Transliteration;

/**
 * Class RubricService
 * @method Rubric getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Content
 */
class RubricService extends EntityService
{
    /** @var RubricRepository */
    protected $repository;

    protected ContentTypeService $content_type_service;

    protected array $ids_by_urls_cache = [];

    /**
     * RubricService constructor.
     * @param string $entity_class_name
     * @param EntityRepository $repository
     * @param CacheService $cache_service
     * @param ContentTypeService $content_type_service
     */
    public function __construct(
        string $entity_class_name,
        EntityRepository $repository,
        CacheService $cache_service,
        ContentTypeService $content_type_service
    ) {
        $this->content_type_service = $content_type_service;

        parent::__construct($entity_class_name, $repository, $cache_service);
    }

    /**
     * @param Rubric|InterfaceEntity $entity_obj
     */
    public function beforeSave(InterfaceEntity $entity_obj): void
    {
        $url = $entity_obj->getUrl();

        if ($url) {
            $url = '/' . ltrim($url, '/');

            if ($url != $entity_obj->getUrl()) {
                UniqueUrl::getUniqueUrl($url);
            }
        } else {
            $url = $this->generateUrl($entity_obj);
            $url = '/' . ltrim($url, '/');
        }

        $entity_obj->setUrl($url);

        parent::beforeSave($entity_obj);
    }

    /**
     * @param int $content_type_id
     * @return array
     */
    public function getIdsArrByContentTypeId(int $content_type_id): array
    {
        return $this->repository->findIdsByContentTypeId($content_type_id);
    }

    /**
     * @param Rubric $rubric_obj
     * @return string
     */
    public function generateUrl(Rubric $rubric_obj): string
    {
        if (!$rubric_obj->getName()) {
            return '';
        }

        $title_for_url = Transliteration::transliteration($rubric_obj->getName());

        $new_url = $title_for_url;
        $new_url = '/' . ltrim($new_url, '/');

        $new_url = substr($new_url, 0, 255);

        $unique_new_url = UniqueUrl::getUniqueUrl($new_url);
        Assert::assert($unique_new_url);

        return $unique_new_url;
    }

    /**
     * @param Rubric $rubric_obj
     * @return null|int
     */
    public function getRelativeTemplateId(Rubric $rubric_obj): ?int
    {
        if ($rubric_obj->getTemplateId()) {
            return $rubric_obj->getTemplateId();
        }

        $content_type_obj = $this->content_type_service->getById($rubric_obj->getContentTypeId());

        return $content_type_obj->getTemplateId();
    }

    /**
     * @param string $url
     * @return null|int
     */
    public function getIdByUrl(string $url): ?int
    {
        if (isset($this->ids_by_urls_cache[$url])) {
            return $this->ids_by_urls_cache[$url];
        }

        $id = $this->repository->findIdByUrl($url);

        $this->ids_by_urls_cache[$url] = $id;

        return $id;
    }
}
