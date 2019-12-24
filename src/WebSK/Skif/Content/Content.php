<?php

namespace WebSK\Skif\Content;

use WebSK\Auth\Auth;
use WebSK\Entity\InterfaceEntity;
use WebSK\Logger\Logger;
use WebSK\Model\FactoryTrait;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceFactory;
use WebSK\Model\InterfaceLoad;
use WebSK\Model\InterfaceSave;
use WebSK\Slim\Container;
use WebSK\Utils\Filters;
use WebSK\Utils\FullObjectId;
use WebSK\Model\ActiveRecord;

/**
 * Class Content
 * @package WebSK\Skif\Content
 */
class Content implements
    InterfaceLoad,
    InterfaceFactory,
    InterfaceSave,
    InterfaceDelete,
    InterfaceEntity
{
    use ActiveRecord;
    use FactoryTrait;

    const ENTITY_SERVICE_CONTAINER_ID = 'skif.content_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'skif.content_repository';
    const DB_TABLE_NAME = 'content';

    const CONTENT_FILES_DIR = 'content';

    const _ID = 'id';
    /** @var int */
    protected $id;

    const _TITLE = 'title';
    /** @var string */
    protected $title = '';

    /** @var string */
    protected $short_title = '';

    /** @var string */
    protected $annotation = '';

    /** @var string */
    protected $body = '';

    const _PUBLISHED_AT = 'published_at';
    /** @var int */
    protected $published_at;

    const _UNPUBLISHED_AT = 'unpublished_at';
    /** @var int */
    protected $unpublished_at;

    const _IS_PUBLISHED = 'is_published';
    /** @var bool */
    protected $is_published = false;

    const _CREATED_AT = 'created_at';
    /** @var int */
    protected $created_at;

    /** @var string */
    protected $image = '';

    /** @var string */
    protected $description = '';

    /** @var string */
    protected $keywords = '';

    const _URL = 'url';
    /** @var string */
    protected $url = '';

    const _CONTENT_TYPE_ID = 'content_type_id';
    /** @var int */
    protected $content_type_id;

    /** @var int */
    protected $last_modified_at;

    /** @var string */
    protected $redirect_url = '';

    /** @var int */
    protected $template_id;

    /** @var int */
    protected $main_rubric_id;

    /**
     * @return int
     */
    public function getContentTypeId()
    {
        return $this->content_type_id;
    }

    /**
     * @param int $content_type_id
     */
    public function setContentTypeId($content_type_id)
    {
        $this->content_type_id = $content_type_id;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return Filters::checkPlain($this->title);
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getShortTitle()
    {
        return $this->short_title;
    }

    /**
     * @param string $short_title
     */
    public function setShortTitle($short_title)
    {
        $this->short_title = $short_title;
    }

    /**
     * @return string
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }

    /**
     * @param string $annotation
     */
    public function setAnnotation($annotation)
    {
        $this->annotation = $annotation;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getPublishedAt()
    {
        return $this->published_at;
    }

    /**
     * @param int $published_at
     */
    public function setPublishedAt($published_at)
    {
        $this->published_at = $published_at;
    }

    /**
     * @return int
     */
    public function getUnpublishedAt()
    {
        return $this->unpublished_at;
    }

    /**
     * @param int $unpublished_at
     */
    public function setUnpublishedAt($unpublished_at)
    {
        $this->unpublished_at = $unpublished_at;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->is_published;
    }

    /**
     * @param bool $is_published
     */
    public function setIsPublished(bool $is_published)
    {
        $this->is_published = $is_published;
    }

    /**
     * @return int
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param int $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getLastModifiedAt()
    {
        return $this->last_modified_at;
    }

    /**
     * @param int $last_modified_at
     */
    public function setLastModifiedAt($last_modified_at)
    {
        $this->last_modified_at = $last_modified_at;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirect_url;
    }

    /**
     * @param string $redirect_url
     */
    public function setRedirectUrl($redirect_url)
    {
        $this->redirect_url = $redirect_url;
    }

    /**
     * @return false|int
     */
    public function getUnixTime()
    {
        return strtotime($this->getPublishedAt());
    }

    /**
     * @return int
     */
    public function getTemplateId()
    {
        return $this->template_id;
    }

    /**
     * @param int $template_id
     */
    public function setTemplateId($template_id)
    {
        $this->template_id = $template_id;
    }

    /**
     * @return int
     */
    public function getMainRubricId()
    {
        return $this->main_rubric_id;
    }

    /**
     * @param int $main_rubric_id
     */
    public function setMainRubricId($main_rubric_id)
    {
        $this->main_rubric_id = $main_rubric_id;
    }

    /**
     * @param array $param_rubrics_ids_arr
     * @return bool
     */
    public function hasRubrics($param_rubrics_ids_arr)
    {
        $content_rubric_service = ContentServiceProvider::getContentRubricService(Container::self());

        $rubrics_ids_arr = $content_rubric_service->getRubricIdsArrByContentId($this->getId());

        foreach ($param_rubrics_ids_arr as $rubric_id) {
            if (in_array($rubric_id, $rubrics_ids_arr)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $rubric_id
     * @return bool
     */
    public function hasRubricId($rubric_id)
    {
        $content_rubric_service = ContentServiceProvider::getContentRubricService(Container::self());

        $rubrics_ids_arr = $content_rubric_service->getRubricIdsArrByContentId($this->getId());

        if (in_array($rubric_id, $rubrics_ids_arr)) {
            return true;
        }

        return false;
    }

    /**
     * @param $id
     */
    public static function afterUpdate($id)
    {
        $content_obj = self::factory($id);

        self::removeObjFromCacheById($id);

        Logger::logObjectEvent($content_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    public function beforeDelete()
    {
        $content_rubric_service = ContentServiceProvider::getContentRubricService(Container::self());
        $content_rubric_service->deleteByContentId($this->getId());

        return true;
    }

    public function afterDelete()
    {
        self::removeObjFromCacheById($this->getId());

        Logger::logObjectEvent($this, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }
}
