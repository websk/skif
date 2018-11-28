<?php
/**
 * @var $url
 * @var $page
 */

use WebSK\Skif\Comment\CommentUtils;
use WebSK\Slim\ConfWrapper;

$message_to_page = ConfWrapper::value('comments.message_to_page', 20);
$count_all_messages = CommentUtils::getCountCommentsByUrl($url);
$all = ceil($count_all_messages / $message_to_page);

$pages_str = '';

for ($i = 1; $i <= $all; $i++) {
    $pages_str .= '<li' . (($i == $page) ? ' class="active"' : '') . '><a>' . $i . '</a></li>';
}

if ($all > 1) {
    echo '<ul class="pagination pagination-sm" id="comment_pager">' . $pages_str . '</ul>';
}
