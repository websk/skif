<?php

/**
 * Class Region
 * @package Skif\Region
     CREATE TABLE `regions` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `title` VARCHAR(255) NOT NULL DEFAULT '',
        `vk_id` INT(11) NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        INDEX `vk_id` (`vk_id`)
     )
 */

namespace Skif\Regions;

class Region implements
    \Skif\Model\InterfaceLoad,
    \Skif\Model\InterfaceSave,
    \Skif\Model\InterfaceDelete
{
    use \Skif\Util\ActiveRecord;
    use \Skif\Model\FactoryTrait;

    const DB_TABLE_NAME = 'regions';

    protected $id;
    protected $title = '';
    protected $vk_id;

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
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getVkId()
    {
        return $this->vk_id;
    }

    /**
     * @param mixed $vk_id
     */
    public function setVkId($vk_id)
    {
        $this->vk_id = $vk_id;
    }

    public function save()
    {
        \Skif\Util\ActiveRecordHelper::saveModelObj($this);

        self::removeObjFromCacheById($this->getId());
    }

    public function delete()
    {
        \Skif\Util\ActiveRecordHelper::deleteModelObj($this);

        self::removeObjFromCacheById($this->getId());
    }
}