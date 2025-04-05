<?php

namespace WebSK\Skif\Blocks;

use WebSK\Auth\Auth;
use WebSK\Auth\User\UserService;
use WebSK\Cache\CacheService;
use WebSK\Entity\EntityRepository;
use WebSK\Entity\EntityService;
use WebSK\Entity\InterfaceEntity;
use WebSK\Logger\Logger;
use WebSK\Utils\Filter;
use WebSK\Utils\FullObjectId;
use WebSK\Utils\Network;
use WebSK\Utils\Url;

/**
 * Class BlockService
 * @method Block getById($entity_id, $exception_if_not_loaded = true)
 * @package WebSK\Skif\Blocks
 */
class BlockService extends EntityService
{
    protected BlockRoleService $block_role_service;

    protected UserService $user_service;

    /** @var BlockRepository */
    protected $repository;

    protected int $cache_ttl_seconds;

    public function __construct(
        string $entity_class_name,
        EntityRepository $repository,
        CacheService $cache_service,
        BlockRoleService $block_role_service,
        UserService $user_service,
        int $cache_ttl_seconds = 60
    ) {
        $this->block_role_service = $block_role_service;
        $this->user_service = $user_service;
        $this->cache_ttl_seconds = $cache_ttl_seconds;

        parent::__construct($entity_class_name, $repository, $cache_service);
    }

    /**
     * @param int $block_id
     * @return void
     * @throws \Exception
     */
    public function deleteBlocksRolesByBlockId(int $block_id): void
    {
        $block_role_ids_arr = $this->block_role_service->getIdsByBlockId($block_id);

        foreach ($block_role_ids_arr as $block_role_id) {
            $block_role_obj = $this->block_role_service->getById($block_role_id);
            $this->block_role_service->delete($block_role_obj);
        }
    }

    /**
     * Массив Block Id в регионе
     * @param null|int $page_region_id
     * @param int $template_id
     * @return array
     */
    public function getBlockIdsArrByPageRegionId(?int $page_region_id, int $template_id): array
    {
        $cache_key = $this->getBlockIdsArrByPageRegionIdCacheKey($page_region_id, $template_id);

        $blocks_ids_arr = $this->cache_service->get($cache_key);
        if ($blocks_ids_arr !== false) {
            return $blocks_ids_arr;
        }

        $blocks_ids_arr = $this->repository->findBlockIdsArrByPageRegionId($page_region_id, $template_id);

        $this->cache_service->set($cache_key, $blocks_ids_arr, 1800);

        return $blocks_ids_arr;
    }

    /**
     * @param null|int $page_region_id
     * @param int $template_id
     * @return string
     */
    protected function getBlockIdsArrByPageRegionIdCacheKey(?int $page_region_id, int $template_id): string
    {
        $cache_key = 'template_id_' . $template_id . '_block_ids_arr_by_page_region_id_';

        if ($page_region_id == PageRegion::BLOCK_REGION_NONE) {
            return $cache_key . '_disabled';
        }

        return $cache_key . $page_region_id;
    }

    /**
     * Массив Id видимых блоков региона в теме
     * @param int $page_region_id
     * @param int $template_id
     * @param string $page_url
     * @return array
     * @throws \Exception
     */
    public function getVisibleBlocksIdsArrByRegionId(
        int $page_region_id,
        int $template_id,
        string $page_url = ''
    ): array {
        if ($page_url == '') {
            // Берем url без $_GET параметров, т.к. это влияет на видимость блоков.
            // Блоки на странице Vidy_sporta/Avtosport$ должны выводиться, например, и по адресу Vidy_sporta/Avtosport
            $page_url = Url::getUriNoQueryString();
        }

        $blocks_ids_arr = $this->getBlockIdsArrByPageRegionId($page_region_id, $template_id);

        $visible_blocks_ids_arr = [];

        $current_user_id = Auth::getCurrentUserId();

        foreach ($blocks_ids_arr as $block_id) {
            if (!$this->blockIsVisibleByUserId($block_id, $current_user_id)) {
                continue;
            }

            if (!$this->blockIsVisibleOnPage($block_id, $page_url)) {
                continue;
            }

            $visible_blocks_ids_arr[] = $block_id;
        }

        return $visible_blocks_ids_arr;
    }

    /**
     * Видимость блока для пользователя
     * @param int|null $block_id
     * @param int|null $user_id
     * @return bool
     */
    protected function blockIsVisibleByUserId(int $block_id, ?int $user_id): bool
    {
        // Проверяем блок на видимость для ролей
        $block_role_ids_arr = $this->block_role_service->getRoleIdsByBlockId($block_id);

        if (!$block_role_ids_arr) {
            return true; // виден всем
        }

        if (!$user_id) {
            return false;
        }

        $user_obj = $this->user_service->getById($user_id, false);
        if (!$user_obj) {
            return false;
        }

        foreach ($block_role_ids_arr as $role_id) {
            if (in_array($role_id, $this->user_service->getRoleIdsArrByUserId($user_id))) {
                return true;
            }
        }

        return false;
    }

    public function getBlockIdsArrByTemplateId(int $template_id): array
    {
        return $this->repository->findBlockIdsArrByTemplateId($template_id);
    }

    /**
     * Видимость блока на странице
     * @param int $block_id
     * @param string $page_url
     * @return bool
     */
    protected function blockIsVisibleOnPage(int $block_id, string $page_url): bool
    {
        $block_obj = $this->getById($block_id);

        if ($block_obj->getPages()) {
            return self::checkBlockComplexVisibility($block_obj->getPages(), $page_url);
        }

        return false;
    }

    /**
     * @param string $pages
     * @param string $real_path
     * @return bool
     */
    protected static function checkBlockComplexVisibility(string $pages, string $real_path = ''): bool
    {
        // parse pages

        $pages = str_replace("\r", "\n", $pages);
        $pages = str_replace("\n\n", "\n", $pages);

        $pages_arr = explode("\n", $pages);

        if (count($pages_arr) == 0) {
            return false;
        }

        // check pages

        $visible = false;

        foreach ($pages_arr as $page_filter_str) {
            $page_filter_str = trim($page_filter_str);

            if (strlen($page_filter_str) > 2) {
                // convert filter string to object
                $filter_obj = new Filter($page_filter_str);

                if ($filter_obj->matchesPage($real_path)) {
                    if ($filter_obj->is_positive) {
                        $visible = true;
                    }

                    if ($filter_obj->is_negative) {
                        $visible = false;
                    }
                }

            }
        }

        return $visible;
    }

    /**
     * @param InterfaceEntity|Block $entity_obj
     */
    public function afterSave(InterfaceEntity $entity_obj): void
    {
        parent::afterSave($entity_obj);

        Logger::logObjectEvent($entity_obj, 'изменение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));
    }

    /**
     * @param InterfaceEntity|Block $entity_obj
     */
    public function afterDelete(InterfaceEntity $entity_obj): void
    {
        $this->deleteBlocksRolesByBlockId($entity_obj->getId());

        $this->clearBlockIdsArrByPageRegionIdCache($entity_obj->getPageRegionId(), $entity_obj->getTemplateId());
        $this->clearBlockIdsArrByPageRegionIdCache(PageRegion::BLOCK_REGION_NONE, $entity_obj->getTemplateId());

        Logger::logObjectEvent($entity_obj, 'удаление', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));

        $this->removeObjFromCacheById($entity_obj->getId());
    }

    /**
     * @param int $block_id
     * @return Block|InterfaceEntity
     */
    public function getBlockObj(int $block_id): Block
    {
        if ($block_id == 'new') {
            return new Block();
        }

        return $this->getById($block_id);
    }

    /**
     * Содержимое блока
     * @param int $block_id
     * @return string
     */
    public function getContentByBlockId(int $block_id): string
    {
        $block_obj = $this->getById($block_id);

        $cache_enabled = true;

        if ($block_obj->getCache() == Block::BLOCK_NO_CACHE) {
            $cache_enabled = false;
        }

        $cache_key = $this->getBlockContentCacheKey($block_obj);

        if ($cache_enabled) {
            $cached_content = $this->cache_service->get($cache_key);

            if ($cached_content !== false) {
                return $cached_content;
            }
        }

        $block_content = $block_obj->getBody();

        if ($block_obj->getFormat() == Block::BLOCK_FORMAT_TYPE_PHP) {
            $block_content = self::evalContentPHPBlock($block_obj);
        }

        if ($cache_enabled) {
            $this->cache_service->set($cache_key, $block_content, $this->cache_ttl_seconds);
        }

        return $block_content;
    }

    /**
     * @param Block $block_obj
     * @return ?string
     */
    protected function getBlockContentCacheKey(Block $block_obj): ?string
    {
        $cid_parts = ['block_content'];
        $cid_parts[] = $block_obj->getId();

        // Кешируем блоки по-полному URL $_SERVER['REQUEST_URI'], в т.ч. с $_GET параметрами.
        // Т.к. содержимое блока может различаться в зависимости от $_GET параметров.
        if ($block_obj->getCache() == Block::BLOCK_CACHE_PER_PAGE) {
            $cid_parts[] = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        if ($block_obj->getCache() == Block::BLOCK_CACHE_PER_USER) {
            $cid_parts[] = Network::getClientIpXff();
        }

        return implode(':', $cid_parts);
    }

    /**
     * Выполняет PHP код в блоке и возвращает результат
     * @param Block $block_obj
     * @return string
     */
    protected function evalContentPHPBlock(Block $block_obj): string
    {
        ob_start();
        print eval('?>'. $block_obj->getBody());
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * @param null|int $page_region_id
     * @param int $template_id
     * @return bool
     */
    public function clearBlockIdsArrByPageRegionIdCache(?int $page_region_id, int $template_id): bool
    {
        $cache_key = self::getBlockIdsArrByPageRegionIdCacheKey($page_region_id, $template_id);

        return $this->cache_service->delete($cache_key);
    }

}