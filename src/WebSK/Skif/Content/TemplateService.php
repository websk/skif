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
     * @return null|int
     */
    public function getIdByName(string $name): ?int
    {
        $cache_key = $this->getTemplateIdByNameCacheKey($name);

        $cache = $this->cache_service->get($cache_key);
        if ($cache !== false) {
            return $cache;
        }

        $template_id = $this->repository->findIdByName($name);

        $this->cache_service->set($cache_key, $template_id, 3600);

        return $template_id;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getTemplateIdByNameCacheKey(string $name): string
    {
        return 'template_id_by_name_' . $name;
    }

    /**
     * @param int|null $template_id
     * @return string
     */
    public function getLayoutFileByTemplateId(?int $template_id): string
    {
        $template_obj = $template_id ? $this->getById($template_id, false) : null;
        if (!$template_obj) {
            return ViewsPath::getSiteViewsPath() . DIRECTORY_SEPARATOR . Template::LAYOUTS_FILES_DIR . DIRECTORY_SEPARATOR. 'layout.main.tpl.php';
        }

        return $template_obj->getLayoutTemplateFilePath();
    }

    /**
     * @param Template|InterfaceEntity $entity_obj
     */
    public function removeFromCache(InterfaceEntity $entity_obj)
    {
        $cache_key = $this->getTemplateIdByNameCacheKey($entity_obj->getName());
        $this->cache_service->delete($cache_key);

        parent::removeFromCache($entity_obj);
    }
}
