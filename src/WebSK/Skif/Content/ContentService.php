<?php

namespace WebSK\Skif\Content;

use WebSK\Auth\Auth;
use WebSK\Cache\CacheService;
use WebSK\Entity\EntityRepository;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Image\ImageManager;
use WebSK\Logger\Logger;
use WebSK\Skif\UniqueUrl;
use WebSK\Utils\Assert;
use WebSK\Utils\FullObjectId;
use WebSK\Utils\Transliteration;

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
    protected array $ids_by_urls_cache = [];

    protected ContentTypeService $content_type_service;

    protected RubricService $rubric_service;

    protected ContentRubricService $content_rubric_service;

    /**
     * ContentService constructor.
     * @param string $entity_class_name
     * @param EntityRepository $repository
     * @param CacheService $cache_service
     * @param ContentTypeService $content_type_service
     * @param RubricService $rubric_service
     * @param ContentRubricService $content_rubric_service
     */
    public function __construct(
        string $entity_class_name,
        EntityRepository $repository,
        CacheService $cache_service,
        ContentTypeService $content_type_service,
        RubricService $rubric_service,
        ContentRubricService $content_rubric_service
    ) {
        $this->content_type_service = $content_type_service;
        $this->rubric_service = $rubric_service;
        $this->content_rubric_service = $content_rubric_service;

        parent::__construct($entity_class_name, $repository, $cache_service);
    }

    /**
     * @param InterfaceEntity|Content $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj): void
    {
        parent::afterDelete($entity_obj);

        Logger::logObjectEvent($entity_obj, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @param InterfaceEntity|Content $entity_obj
     * @param string $message
     * @return bool
     */
    public function canDelete(InterfaceEntity $entity_obj, string &$message): bool
    {
        $this->content_rubric_service->deleteByContentId($entity_obj->getId());

        $this->deleteImage($entity_obj);

        return true;
    }

    /**
     * @param InterfaceEntity|Content $entity_obj
     */
    public function beforeSave(InterfaceEntity $entity_obj): void
    {
        $current_time = date('Y-m-d H:i:s');

        $entity_obj->setLastModifiedAt($current_time);

        if ($entity_obj->isPublished() && !$entity_obj->getPublishedAt()) {
            $entity_obj->setPublishedAt($current_time);
        }

        // URL
        if (!$entity_obj->getId() || !$entity_obj->isPublished()) {
            $url = $entity_obj->getUrl();

            if (!$url) {
                $url = $this->generateUrl($entity_obj);
            }

            $url = '/' . ltrim($url, '/');

            $content_type_id = $entity_obj->getContentTypeId();
            $content_type_obj = $this->content_type_service->getById($content_type_id);

            $content_type_url_length = strlen($content_type_obj->getUrl());
            if (substr($url, 0, $content_type_url_length + 1) != $content_type_obj->getUrl() . '/') {
                $url = $content_type_obj->getUrl() . $url;
            }

            $url = '/' . ltrim($url, '/');

            $entity_obj->setUrl($url);
        }

        parent::beforeSave($entity_obj);
    }

    /**
     * @param InterfaceEntity|Content $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj): void
    {
        parent::afterSave($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @param Content $content_obj
     * @param array $param_rubrics_ids_arr
     * @return bool
     */
    public function hasRubrics(Content $content_obj, array $param_rubrics_ids_arr): bool
    {
        $rubrics_ids_arr = $this->content_rubric_service->getRubricIdsArrByContentId($content_obj->getId());

        foreach ($param_rubrics_ids_arr as $rubric_id) {
            if (in_array($rubric_id, $rubrics_ids_arr)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Content $content_obj
     * @param int $rubric_id
     * @return bool
     */
    public function hasRubricId(Content $content_obj, int $rubric_id): bool
    {
        $rubrics_ids_arr = $this->content_rubric_service->getRubricIdsArrByContentId($content_obj->getId());

        if (in_array($rubric_id, $rubrics_ids_arr)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $content_type
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public function getIdsArrByType(string $content_type, int $limit_to_page = 0, int $page = 1): array
    {
        $content_type_obj = $this->content_type_service->getByType($content_type);

        return $this->repository->findIdsByContentTypeId($content_type_obj->getId(), $limit_to_page, $page);
    }

    /**
     * @param string $content_type
     * @return int
     */
    public function getCountContentsByType(string $content_type): int
    {
        $contents_ids_arr = $this->getIdsArrByType($content_type);

        return count($contents_ids_arr);
    }

    /**
     * @param string $content_type
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public function getPublishedIdsArrByType(string $content_type, int $limit_to_page = 0, int $page = 1): array
    {
        $content_type_obj = $this->content_type_service->getByType($content_type);

        return $this->repository->findPublishedIdsByContentTypeId($content_type_obj->getId(), $limit_to_page, $page);
    }

    /**
     * @param string $content_type
     * @return int
     */
    public function getCountPublishedContentsByType(string $content_type): int
    {
        $contents_ids_arr = $this->getPublishedIdsArrByType($content_type);

        return count($contents_ids_arr);
    }

    /**
     * @param string $url
     * @return int
     */
    public function getIdByUrl(string $url): int
    {
        if (isset($this->ids_by_urls_cache[$url])) {
            return $this->ids_by_urls_cache[$url];
        }

        $id = $this->repository->findIdByUrl($url);

        $this->ids_by_urls_cache[$url] = $id;

        return $id;
    }

    /**
     * @param Content $content_obj
     * @return int|null
     */
    public function getRelativeTemplateId(Content $content_obj): ?int
    {
        if ($content_obj->getTemplateId()) {
            return $content_obj->getTemplateId();
        }

        if ($content_obj->getMainRubricId()) {
            $main_rubric_obj = $this->rubric_service->getById($content_obj->getMainRubricId());

            return $main_rubric_obj->getTemplateId();
        }

        $content_type_obj = $this->content_type_service->getById($content_obj->getContentTypeId());

        return $content_type_obj->getTemplateId();
    }

    /**
     * @param Content $content_obj
     * @return string
     */
    protected function generateUrl(Content $content_obj): string
    {
        if (!$content_obj->getTitle()) {
            return '';
        }

        if ($content_obj->isPublished()) {
            throw new \Exception('Невозможно сгенерировать URL для опубликованного контента. Сначала распубликуйте материал.');
        }

        $title_for_url = Transliteration::transliteration($content_obj->getTitle());

        $content_type_id = $content_obj->getContentTypeId();
        $content_type_obj = $this->content_type_service->getById($content_type_id);

        $new_url = $content_type_obj->getUrl() . '/' . $title_for_url;
        $new_url = '/' . ltrim($new_url, '/');

        $new_url = substr($new_url, 0, 255);

        $unique_new_url = UniqueUrl::getUniqueUrl($new_url);
        Assert::assert($unique_new_url);

        return $unique_new_url;
    }

    /**
     * @param Content $content_obj
     * @return string
     */
    public function getImagePath(Content $content_obj): string
    {
        if (!$content_obj->getImage()) {
            return '';
        }

        $content_type_id = $content_obj->getContentTypeId();
        $content_type_obj = $this->content_type_service->getById($content_type_id);

        return Content::CONTENT_FILES_DIR . '/' . $content_type_obj->getType() . '/' . $content_obj->getImage();
    }

    /**
     * @param Content $content_obj
     */
    public function deleteImage(Content $content_obj): void
    {
        if (!$content_obj->getImage()) {
            return;
        }

        $image_manager = new ImageManager();
        $image_manager->removeImageFile($this->getImagePath($content_obj));

        $content_obj->setImage('');
        $this->save($content_obj);
    }

    /**
     * @param string $title
     * @return array
     */
    public function getIdsArrByTitle(string $title): array
    {
        return $this->repository->findIdsByTitle($title);
    }
}
