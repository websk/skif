<?php
/**
 * @var $url
 * @var $page
 */

$message_to_page = \Skif\Conf\ConfWrapper::value('comments.message_to_page', 20);
$count_all_messages = \Skif\Comments\CommentsUtils::getCountCommentsByUrl($url);
$all = ceil($count_all_messages / $message_to_page);

$pages_str = '';

for ($i = 1; $i <= $all; $i++) {
    $pages_str .= '<li' . (($i == $page) ? ' class="active"' : '') . '><a>' . $i . '</a></li>';
}

if ($all > 1) {
    echo '<ul class="pagination pagination-sm" id="comment_pager">' . $pages_str . '</ul>';
}