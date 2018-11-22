<?php

namespace Skif\SiteMenu;

class SiteMenu implements
    \WebSK\Model\InterfaceLoad,
    \WebSK\Model\InterfaceFactory,
    \WebSK\Model\InterfaceSave,
    \WebSK\Model\InterfaceDelete
{
    use WebSK\Model\ActiveRecord;
    use WebSK\Model\FactoryTrait;

    protected $id;
    protected $name;
    protected $url;

    const DB_TABLE_NAME = 'site_menu';


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
    public function getName()
    {
        return \Skif\Utils::checkPlain($this->name);
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
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

}