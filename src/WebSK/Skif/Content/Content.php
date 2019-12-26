<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\Entity;
use WebSK\Utils\Filters;

/**
 * Class Content
 * @package WebSK\Skif\Content
 */
class Content extends Entity
{
    const ENTITY_SERVICE_CONTAINER_ID = 'skif.content_service';
    const ENTITY_REPOSITORY_CONTAINER_ID = 'skif.content_repository';
    const DB_TABLE_NAME = 'content';

    const CONTENT_FILES_DIR = 'content';

    const _TITLE = 'title';
    /** @var string */
    protected $title = '';

    /** @var string */
    protected $short_title = '';

    const _ANNOTATION = 'annotation';
    /** @var string */
    protected $annotation = '';

    const _BODY = 'body';
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

    const _IMAGE = 'image';
    /** @var string */
    protected $image = '';

    const _DESCRIPTION = 'description';
    /** @var string */
    protected $description = '';

    const _KEYWORDS = 'keywords';
    /** @var string */
    protected $keywords = '';

    const _URL = 'url';
    /** @var string */
    protected $url = '';

    const _CONTENT_TYPE_ID = 'content_type_id';
    /** @var null|int */
    protected $content_type_id;

    const _LAST_MODIFIED_AT = 'last_modified_at';
    /** @var int */
    protected $last_modified_at;

    const _REDIRECT_URL = 'redirect_url';
    /** @var string */
    protected $redirect_url = '';

    const _TEMPLATE_ID = 'template_id';
    /** @var null|int */
    protected $template_id;

    const _MAIN_RUBRIC_ID = 'main_rubric_id';
    /** @var null|int */
    protected $main_rubric_id;

    /**
     * @return int|null
     */
    public function getContentTypeId(): ?int
    {
        return $this->content_type_id;
    }

    /**
     * @param int|null $content_type_id
     */
    public function setContentTypeId(?int $content_type_id): void
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
     * @return int|null
     */
    public function getTemplateId(): ?int
    {
        return $this->template_id;
    }

    /**
     * @param int|null $template_id
     */
    public function setTemplateId(?int $template_id): void
    {
        $this->template_id = $template_id;
    }

    /**
     * @return int|null
     */
    public function getMainRubricId(): ?int
    {
        return $this->main_rubric_id;
    }

    /**
     * @param int|null $main_rubric_id
     */
    public function setMainRubricId(?int $main_rubric_id): void
    {
        $this->main_rubric_id = $main_rubric_id;
    }
}
