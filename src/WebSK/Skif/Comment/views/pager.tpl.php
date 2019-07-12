<?php
/**
 * @var $url
 * @var $page
 * @var $count_comments
 * @var $message_to_page
 */

$all = ceil($count_comments / $message_to_page);

$pages_str = '';

for ($i = 1; $i <= $all; $i++) {
    $pages_str .= '<li' . (($i == $page) ? ' class="active"' : '') . '><a>' . $i . '</a></li>';
}

if ($all > 1) {
    echo '<ul class="pagination pagination-sm" id="comment_pager">' . $pages_str . '</ul>';
}
