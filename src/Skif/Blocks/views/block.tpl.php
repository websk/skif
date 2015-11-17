<?php
/**
 * @var int $block_id
 */

$block_content = \Skif\Blocks\BlockUtils::getContentByBlockId($block_id);
if ($block_content == '') {
    return;
}

echo '<!-- ' . $block_id . ' -->';
echo $block_content;
echo '<!-- /' . $block_id . ' -->';
