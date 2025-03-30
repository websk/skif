<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\Entity;

/**
 * Class Content
 * @package WebSK\Skif\Content
 */
class Content extends Entity
{
    const string DB_TABLE_NAME = 'content';

    const string CONTENT_FILES_DIR = 'content';

    const string _TITLE = 'title';
    protected string $title;

    const string _SHORT_TITLE = 'short_title';
    protected string $short_title = '';

    const string _ANNOTATION = 'annotation';
    protected string $annotation = '';

    const string _BODY = 'body';
    protected string $body = '';

    const string _PUBLISHED_AT = 'published_at';
    protected ?string $published_at = null;

    const string _UNPUBLISHED_AT = 'unpublished_at';
    protected ?string $unpublished_at = null;

    const string _IS_PUBLISHED = 'is_published';
    protected bool $is_published = false;

    const string _IMAGE = 'image';
    protected ?string $image = null;

    const string _DESCRIPTION = 'description';
    protected string $description = '';

    const string _KEYWORDS = 'keywords';
    protected string $keywords = '';

    const string _URL = 'url';
    protected ?string $url = null;

    const string _CONTENT_TYPE_ID = 'content_type_id';
    protected ?int $content_type_id = null;

    const string _LAST_MODIFIED_AT = 'last_modified_at';
    protected ?string $last_modified_at = null;

    const string _REDIRECT_URL = 'redirect_url';
    protected ?string $redirect_url = null;

    const string _TEMPLATE_ID = 'template_id';
    protected ?int $template_id = null;

    const string _MAIN_RUBRIC_ID = 'main_rubric_id';
    protected ?int $main_rubric_id = null;

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
     * @return null|string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param null|string $image
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getShortTitle(): string
    {
        return $this->short_title;
    }

    /**
     * @param string $short_title
     */
    public function setShortTitle(string $short_title): void
    {
        $this->short_title = $short_title;
    }

    /**
     * @return string
     */
    public function getAnnotation(): string
    {
        return $this->annotation;
    }

    /**
     * @param string $annotation
     */
    public function setAnnotation(string $annotation): void
    {
        $this->annotation = $annotation;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string|null
     */
    public function getPublishedAt(): ?string
    {
        return $this->published_at;
    }

    /**
     * @param string|null $published_at
     */
    public function setPublishedAt(?string $published_at): void
    {
        $this->published_at = $published_at;
    }

    /**
     * @return string|null
     */
    public function getUnpublishedAt(): ?string
    {
        return $this->unpublished_at;
    }

    /**
     * @param string|null $unpublished_at
     */
    public function setUnpublishedAt(?string $unpublished_at): void
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
    public function setIsPublished(bool $is_published): void
    {
        $this->is_published = $is_published;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords(string $keywords): void
    {
        $this->keywords = $keywords;
    }

    /**
     * @return null|string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param null|string $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string|null
     */
    public function getLastModifiedAt(): ?string
    {
        return $this->last_modified_at;
    }

    /**
     * @param string|null $last_modified_at
     */
    public function setLastModifiedAt(?string $last_modified_at): void
    {
        $this->last_modified_at = $last_modified_at;
    }

    /**
     * @return null|string
     */
    public function getRedirectUrl(): ?string
    {
        return $this->redirect_url;
    }

    /**
     * @param null|string $redirect_url
     */
    public function setRedirectUrl(?string $redirect_url): void
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
