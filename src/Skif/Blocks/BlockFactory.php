<?php

namespace Skif\Blocks;


class BlockFactory extends \Skif\Factory
{

    /**
     * @param $block_id
     * @return \Skif\Blocks\Block|null
     */
    public static function loadBlockObj($block_id)
    {
        $class_name = '\Skif\Blocks\Block';

        return self::createAndLoadObject(
            $class_name,
            $block_id
        );
    }

    public static function removeFromCacheById($block_id)
    {
        \Skif\Factory::removeObjectFromCache('\Skif\Blocks\Block', $block_id);
    }
} 