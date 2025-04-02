<?php

namespace WebSK\Skif\Blocks;

use WebSK\Auth\Auth;
use WebSK\Entity\Entity;
use WebSK\Model\ActiveRecordHelper;
use WebSK\Model\Factory;
use WebSK\Utils\Assert;

/**
 * Class PageRegion
 * @package WebSK\Skif\Blocks
 */
class PageRegion extends Entity
{

    const string DB_TABLE_NAME = 'page_regions';

    public const null BLOCK_REGION_NONE = null;

    protected string $name;

    protected int $template_id;

    protected string $title;


    public static function factory(?int $id_to_load, bool $exception_if_not_loaded = true)
    {
        if ($id_to_load == self::BLOCK_REGION_NONE) {
            $obj = new PageRegion();
            $obj->setName('disabled');
            $obj->setTitle('Отключенные блоки');

            return $obj;
        }

        $class_name = self::getMyGlobalizedClassName();
        $obj = Factory::createAndLoadObject($class_name, $id_to_load);

        if ($exception_if_not_loaded) {
            Assert::assert($obj);
        }

        return $obj;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getTemplateId(): int
    {
        return $this->template_id;
    }

    /**
     * @param int $template_id
     */
    public function setTemplateId(int $template_id): void
    {
        $this->template_id = $template_id;
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

}
