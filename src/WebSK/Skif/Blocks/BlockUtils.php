<?php

namespace WebSK\Skif\Blocks;

use WebSK\Slim\Container;

/**
 * Class BlockUtils
 * @package WebSK\Skif\Blocks
 */
class BlockUtils
{
    public const string COOKIE_CURRENT_TEMPLATE_ID = 'skif_blocks_current_template_id';

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

    /**
     * @param int $block_id
     * @return Block
     */
    public static function getBlockObj(int $block_id): Block
    {
        if ($block_id == 'new') {
            return new Block();
        }

        $container = Container::self();
        $block_service = $container->get(BlockService::class);

        return $block_service->getById($block_id);
    }
}
