<?php
/**
 * @var int $block_id
 */

use WebSK\Skif\Blocks\BlockUtils;

$block_content = BlockUtils::getContentByBlockId($block_id);
if ($block_content == '') {
    return;
}

echo '<!-- ' . $block_id . ' -->';
echo $block_content;
echo '<!-- /' . $block_id . ' -->';
