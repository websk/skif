<?php
/**
 * @var $block
 */

echo '
<!-- block '. $block->id .' -->
';

if (is_string($block->content)) {
    echo $block->content;
}
echo '
<!-- /block -->
';