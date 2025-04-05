<?php
/**
 * @var int $block_id
 * @var BlockService $block_service
 */

use WebSK\Skif\Blocks\BlockService;

$block_content = $block_service->getContentByBlockId($block_id);
if ($block_content == '') {
    return;
}

echo '<!-- ' . $block_id . ' -->';
echo $block_content;
echo '<!-- /' . $block_id . ' -->';
