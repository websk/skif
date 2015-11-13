<?php
/*
CREATE TABLE `content` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL DEFAULT '',
    `short_title` VARCHAR(255) NOT NULL DEFAULT '',
    `annotation` TEXT NOT NULL,
    `body` MEDIUMTEXT NOT NULL,
    `published_at` DATE NULL DEFAULT NULL,
    `unpublished_at` DATE NULL DEFAULT NULL,
    `is_published` SMALLINT(6) NOT NULL DEFAULT '0',
    `created_at` DATETIME NULL DEFAULT NULL,
    `image` VARCHAR(100) NOT NULL DEFAULT '',
    `description` VARCHAR(255) NOT NULL DEFAULT '',
    `keywords` VARCHAR(255) NOT NULL DEFAULT '',
    `url` VARCHAR(1000) NOT NULL DEFAULT '',
    `type` CHAR(20) NOT NULL DEFAULT '',
    `last_modified_at` DATETIME NOT NULL,
    `redirect_url` VARCHAR(1000) NOT NULL DEFAULT '',
    `template_id` INT(11) NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `rubric_id` (`rubric_id`),
    INDEX `type` (`type`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
*/

namespace Skif\Content;

class Content implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceFactory,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete,
    \Skif\Model\InterfaceLogger
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    protected $id;
    protected $title = '';
    protected $short_title = '';
    protected $annotation = '';
    protected $body = '';
    protected $rubric_id;
    protected $published_at;
    protected $unpublished_at;
    protected $is_published;
    protected $created_at;
    protected $image = '';
    protected $description = '';
    protected $keywords = '';
    protected $url = '';
    protected $type = 'page';
    protected $last_modified_at;
    protected $redirect_url = '';
    protected $template_id;
    protected $content_rubrics_ids_arr;

    public static $active_record_ignore_fields_arr = array(
        'content_rubrics_ids_arr',
        'rubric_id'
    );

    const DB_TABLE_NAME = 'content';

    public function getEditorUrl()
    {
        return '/admin/content/' . $this->getType() . '/edit/' . $this->getId();
    }

    public function load($id)
    {
        $is_loaded = \Skif\Util\ActiveRecordHelper::loadModelObj($this, $id);
        if (!$is_loaded) {
            return false;
        }

        $query = "SELECT id FROM " . \Skif\Content\ContentRubrics::DB_TABLE_NAME ." WHERE content_id = ?";
        $this->content_rubrics_ids_arr = \Skif\DB\DBWrapper::readColumn(
            $query,
            array($this->id)
        );

        return true;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
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

        return 'content/' . $this->getType() . '/' . $this->image;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return \Skif\Utils::checkPlain($this->title);
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getShortTitle()
    {
        return $this->short_title;
    }

    /**
     * @param mixed $short_title
     */
    public function setShortTitle($short_title)
    {
        $this->short_title = $short_title;
    }

    /**
     * @return mixed
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }

    /**
     * @param mixed $annotation
     */
    public function setAnnotation($annotation)
    {
        $this->annotation = $annotation;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getPublishedAt()
    {
        return $this->published_at;
    }

    /**
     * @param mixed $published_at
     */
    public function setPublishedAt($published_at)
    {
        $this->published_at = $published_at;
    }

    /**
     * @return mixed
     */
    public function getUnpublishedAt()
    {
        return $this->unpublished_at;
    }

    /**
     * @param mixed $unpublished_at
     */
    public function setUnpublishedAt($unpublished_at)
    {
        $this->unpublished_at = $unpublished_at;
    }

    /**
     * @return mixed
     */
    public function isPublished()
    {
        return $this->is_published;
    }

    /**
     * @param mixed $is_published
     */
    public function setIsPublished($is_published)
    {
        $this->is_published = $is_published;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param mixed $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function generateUrl()
    {
        if (!$this->getTitle()) {
            return '';
        }

        if ($this->isPublished()) {
            return '';
        }

        $content_type = $this->getType();

        $title_for_url = \Skif\Translit::translit($this->getTitle());

        $content_type_obj = \Skif\Content\ContentTypeFactory::loadContentTypeByType($content_type);
        \Skif\Utils::assert($content_type_obj);

        $new_url = $content_type_obj->getUrl() . '/' . $title_for_url;
        $new_url = '/' . ltrim($new_url, '/');

        $new_url = substr($new_url, 0, 255);

        $unique_new_url = \Skif\UrlManager::getUniqueUrl($new_url);
        \Skif\Utils::assert($unique_new_url);

        return $unique_new_url;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getLastModifiedAt()
    {
        return $this->last_modified_at;
    }

    /**
     * @param mixed $last_modified_at
     */
    public function setLastModifiedAt($last_modified_at)
    {
        $this->last_modified_at = $last_modified_at;
    }

    /**
     * @return mixed
     */
    public function getRedirectUrl()
    {
        return $this->redirect_url;
    }

    /**
     * @param mixed $redirect_url
     */
    public function setRedirectUrl($redirect_url)
    {
        $this->redirect_url = $redirect_url;
    }


    public function getUnixTime()
    {
        return strtotime($this->getPublishedAt());
    }

    /**
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->template_id;
    }

    /**
     * @param mixed $template_id
     */
    public function setTemplateId($template_id)
    {
        $this->template_id = $template_id;
    }

    /**
     * @return mixed
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
            $content_rubrics_obj = \Skif\Content\ContentRubrics::factory($content_rubrics_id);

            $rubric_ids_arr[] = $content_rubrics_obj->getRubricId();
        }

        return $rubric_ids_arr;
    }

    public function getCountRubricIdsArr() {
        return count($this->getContentRubricsIdsArr());
    }

    public function hasRubrics($rubrics_ids_arr)
    {
        $content_rubrics_ids_arr = $this->getContentRubricsIdsArr();

        if (array_intersect($rubrics_ids_arr, $content_rubrics_ids_arr) == $rubrics_ids_arr) {
            return true;
        }

        return false;
    }

    public function deleteContentRubrics() {
        $content_rubrics_ids_arr = $this->getContentRubricsIdsArr();
        foreach ($content_rubrics_ids_arr as $content_rubrics_id) {
            $content_rubrics_obj = \Skif\Content\ContentRubrics::factory($content_rubrics_id);

            $content_rubrics_obj->delete();
        }
    }

    public function beforeDelete()
    {
        $this->deleteContentRubrics();

        return true;
    }
}