<?php

namespace WebSK\Skif\Content;

use WebSK\Auth\Auth;
use WebSK\Entity\InterfaceEntity;
use WebSK\Logger\Logger;
use WebSK\DB\DBWrapper;
use WebSK\Model\FactoryTrait;
use WebSK\Model\InterfaceDelete;
use WebSK\Model\InterfaceFactory;
use WebSK\Model\InterfaceLoad;
use WebSK\Model\InterfaceSave;
use WebSK\Skif\UniqueUrl;
use WebSK\Utils\Filters;
use WebSK\Utils\FullObjectId;
use WebSK\Utils\Transliteration;
use WebSK\Model\ActiveRecord;
use WebSK\Model\ActiveRecordHelper;
use WebSK\Utils\Assert;

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

    /** @var int */
    protected $published_at;

    /** @var int */
    protected $unpublished_at;

    /** @var int */
    protected $is_published = 0;

    /** @var int */
    protected $created_at;

    /** @var string */
    protected $image = '';

    /** @var string */
    protected $description = '';

    /** @var string */
    protected $keywords = '';

    /** @var string */
    protected $url = '';

    /** @var int */
    protected $content_type_id;

    /** @var int */
    protected $last_modified_at;

    /** @var string */
    protected $redirect_url = '';

    /** @var int */
    protected $template_id;

    /** @var array */
    protected $content_rubrics_ids_arr = [];

    /** @var int */
    protected $main_rubric_id;

    public static $active_record_ignore_fields_arr = [
        'content_rubrics_ids_arr',
    ];

    const DB_TABLE_NAME = 'content';

    /**
     * @return string
     */
    public function getEditorUrl()
    {
        $content_type_id = $this->getContentTypeId();
        $content_type_obj = ContentType::factory($content_type_id);

        return '/admin/content/' . $content_type_obj->getType() . '/edit/' . $this->getId();
    }

    /**
     * @param $id
     * @return bool
     */
    public function load($id)
    {
        $is_loaded = ActiveRecordHelper::loadModelObj($this, $id);
        if (!$is_loaded) {
            return false;
        }

        $query = "SELECT id FROM " . ContentRubrics::DB_TABLE_NAME . " WHERE content_id = ?";
        $this->content_rubrics_ids_arr = DBWrapper::readColumn(
            $query,
            [$this->id]
        );

        return true;
    }

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
     * @return string
     */
    public function getImagePath()
    {
        if (!$this->getImage()) {
            return '';
        }

        $content_type_id = $this->getContentTypeId();
        $content_type_obj = ContentType::factory($content_type_id);

        return 'content/' . $content_type_obj->getType() . '/' . $this->image;
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
    public function isPublished()
    {
        return (bool)$this->is_published;
    }

    /**
     * @param int $is_published
     */
    public function setIsPublished($is_published)
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
     * @return bool|string
     */
    public function generateUrl()
    {
        if (!$this->getTitle()) {
            return '';
        }

        if ($this->isPublished()) {
            return '';
        }


        $title_for_url = Transliteration::transliteration($this->getTitle());

        $content_type_id = $this->getContentTypeId();
        $content_type_obj = ContentType::factory($content_type_id);

        $new_url = $content_type_obj->getUrl() . '/' . $title_for_url;
        $new_url = '/' . ltrim($new_url, '/');

        $new_url = substr($new_url, 0, 255);

        $unique_new_url = UniqueUrl::getUniqueUrl($new_url);
        Assert::assert($unique_new_url);

        return $unique_new_url;
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
    public function getRelativeTemplateId()
    {
        if ($this->getTemplateId()) {
            return $this->getTemplateId();
        }

        if ($this->getMainRubricId()) {
            $main_rubric_obj = Rubric::factory($this->getMainRubricId());

            return $main_rubric_obj->getTemplateId();
        }

        $content_type_obj = ContentType::factory($this->getContentTypeId());

        return $content_type_obj->getTemplateId();
    }

    /**
     * @return array
     */
    public function getContentRubricsIdsArr()
    {
        return $this->content_rubrics_ids_arr;
    }

    /**
     * Array ContentRubrics ID
     * @return array
     */
    public function getRubricIdsArr()
    {
        $content_rubrics_ids_arr = $this->getContentRubricsIdsArr();

        $rubric_ids_arr = array();

        foreach ($content_rubrics_ids_arr as $content_rubrics_id) {
            $content_rubrics_obj = ContentRubrics::factory($content_rubrics_id);

            $rubric_ids_arr[] = $content_rubrics_obj->getRubricId();
        }

        return $rubric_ids_arr;
    }

    /**
     * @return int
     */
    public function getCountRubricIdsArr()
    {
        return count($this->getContentRubricsIdsArr());
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
        $rubrics_ids_arr = $this->getRubricIdsArr();

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
        $rubrics_ids_arr = $this->getRubricIdsArr();

        if (in_array($rubric_id, $rubrics_ids_arr)) {
            return true;
        }

        return false;
    }

    public function deleteContentRubrics()
    {
        $content_rubrics_ids_arr = $this->getContentRubricsIdsArr();
        foreach ($content_rubrics_ids_arr as $content_rubrics_id) {
            $content_rubrics_obj = ContentRubrics::factory($content_rubrics_id);

            $content_rubrics_obj->delete();
        }
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
        $this->deleteContentRubrics();

        return true;
    }

    public function afterDelete()
    {
        self::removeObjFromCacheById($this->getId());

        Logger::logObjectEvent($this, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }
}
