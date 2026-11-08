<?php
/**
 * @var string $url
 * @var int $page
 * @var int $count_comments
 * @var int $message_to_page
 */

$all = ceil($count_comments / $message_to_page);

$pages_str = '';

for ($i = 1; $i <= $all; $i++) {
    $pages_str .= '<li class="page-item' . (($i == $page) ? ' active' : '') . '"><button type="button" class="page-link"' . (($i == $page) ? ' aria-current="page"' : '') . '>' . $i . '</button></li>';
}

if ($all > 1) {
    echo '<nav aria-label="Страницы комментариев"><ul class="pagination pagination-sm mb-0" id="comment_pager">' . $pages_str . '</ul></nav>';
}
