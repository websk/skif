<?php

namespace WebSK\Skif\Blocks;

use WebSK\Config\ConfWrapper;
use WebSK\Utils\Network;
use WebSK\Cache\CacheWrapper;

/**
 * Class BlockUtils
 * @package WebSK\Skif\Blocks
 */
class BlockUtils
{

    /**
     * Содержимое блока
     * @param int $block_id
     * @return string
     */
    public static function getContentByBlockId(int $block_id): string
    {
        $block_obj = Block::factory($block_id);

        $cache_enabled = true;

        if ($block_obj->getCache() == Block::BLOCK_NO_CACHE) {
            $cache_enabled = false;
        }


        $cache_key = self::getBlockContentCacheKey($block_id);

        if ($cache_enabled) {
            $cached_content = CacheWrapper::get($cache_key);

            if ($cached_content !== false) {
                return $cached_content;
            }
        }

        $block_content = $block_obj->getBody();

        if ($block_obj->getFormat() == Block::BLOCK_FORMAT_TYPE_PHP) {
            $block_content = self::evalContentPHPBlock($block_obj);
        }

        if ($cache_enabled) {
            $cache_ttl_seconds = ConfWrapper::value('cache.expire', 60);
            CacheWrapper::set($cache_key, $block_content, $cache_ttl_seconds);
        }

        return $block_content;
    }

    /**
     * Выполняет PHP код в блоке и возвращает результат
     * @param Block $block_obj
     * @return string
     */
    public static function evalContentPHPBlock(Block $block_obj): string
    {
        ob_start();
        print eval('?>'. $block_obj->getBody());
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * @param int $block_id
     * @return ?string
     */
    protected static function getBlockContentCacheKey(int $block_id): ?string
    {
        $block_obj = Block::factory($block_id);

        $cid_parts = ['block_content'];
        $cid_parts[] = $block_obj->getId();

        // Кешируем блоки по полному урлу $_SERVER['REQUEST_URI'], в т.ч. с $_GET параметрами.
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
     * @param null|int $page_region_id
     * @param int $template_id
     */
    public static function clearBlockIdsArrByPageRegionIdCache(?int $page_region_id, int $template_id)
    {
        $cache_key = self::getBlockIdsArrByPageRegionIdCacheKey($page_region_id, $template_id);
        CacheWrapper::delete($cache_key);
    }

    /**
     * @param null|int $page_region_id
     * @param int $template_id
     * @return string
     */
    protected static function getBlockIdsArrByPageRegionIdCacheKey(?int $page_region_id, int $template_id): string
    {
        $cache_key = 'template_id_' . $template_id . '_block_ids_arr_by_page_region_id_';

        if ($page_region_id == PageRegion::BLOCK_REGION_NONE) {
            return $cache_key . '_disabled';
        }

        return $cache_key . $page_region_id;
    }

    /**
     * Массив возможных форматов блока
     * @return array
     */
    public static function getFormatsArr(): array
    {
        return array(
            Block::BLOCK_FORMAT_TYPE_PLAIN => 'Текст',
            Block::BLOCK_FORMAT_TYPE_HTML => 'HTML',
            Block::BLOCK_FORMAT_TYPE_PHP => 'PHP code'
        );
    }

    /**
     * Массив способов кеширования блока
     * @return array
     */
    public static function getCachesArr(): array
    {
        return array(
            Block::BLOCK_NO_CACHE => 'не кэшировать',
            Block::BLOCK_CACHE_PER_USER => 'кэшировать для каждого пользователя',
            Block::BLOCK_CACHE_PER_PAGE => 'кэшировать для каждого урла',
            Block::BLOCK_CACHE_GLOBAL => 'кэшировать глобально'
        );
    }

    /**
     * Тема
     * @return string
     */
    public static function getCurrentTemplateId(): int
    {
        if (array_key_exists(self::COOKIE_CURRENT_TEMPLATE_ID, $_COOKIE)) {
            return $_COOKIE[self::COOKIE_CURRENT_TEMPLATE_ID];
        }

        return 1;
    }

    public static function setCurrentTemplateId(int $template_id): void
    {
        $delta = null;
        setcookie(BlockUtils::COOKIE_CURRENT_TEMPLATE_ID, $template_id, $delta, '/');
    }
}
