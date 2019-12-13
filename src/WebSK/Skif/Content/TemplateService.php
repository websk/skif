<?php

namespace WebSK\Skif\Content;

use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Views\ViewsPath;

/**
 * Class TemplateService
 * @method Template getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Content
 */
class TemplateService extends EntityService
{
    /** @var TemplateRepository */
    protected $repository;

    /**
     * @param string $name
     * @return false|int
     */
    public function getIdByName(string $name)
    {
        $cache_key = $this->getTemplateIdByNameCacheKey($name);

        $cache = $this->cache_service->get($cache_key);
        if ($cache !== false) {
            return $cache;
        }

        $template_id = $this->repository->findIdByName($name);

        if ($template_id === false) {
            $template_id = null;
        }

        $this->cache_service->set($cache_key, $template_id, 3600);

        return $template_id;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getTemplateIdByNameCacheKey(string $name)
    {
        return 'template_id_by_name_' . $name;
    }

    /**
     * @param int $template_id
     * @return string
     */
    public function getLayoutFileByTemplateId(int $template_id)
    {
        $template_obj = $this->getById($template_id, false);
        if (!$template_obj) {
            return ViewsPath::getSiteViewsPath() . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR. 'layout.main.tpl.php';
        }

        return $template_obj->getLayoutTemplateFilePath();
    }

    /**
     * @param Template|InterfaceEntity $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj)
    {
        $cache_key = $this->getTemplateIdByNameCacheKey($entity_obj->getName());
        $this->cache_service->delete($cache_key);

        parent::afterSave($entity_obj);
    }

    /**
     * @param Template|InterfaceEntity $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj)
    {
        $cache_key = $this->getTemplateIdByNameCacheKey($entity_obj->getName());
        $this->cache_service->delete($cache_key);

        parent::afterDelete($entity_obj);
    }
}
