<?php
/**
 * @var $content_type
 */

use WebSK\Skif\Pager;
use WebSK\Skif\PhpRender;
use WebSK\Slim\ConfWrapper;
use WebSK\Skif\Content\Content;
use WebSK\Skif\Content\ContentUtils;

$page = array_key_exists('p', $_GET) ? $_GET['p'] : 1;
$limit_to_page = ConfWrapper::value('content.' . $content_type . '.limit_to_page');
$current_date = date('Y-m-d');
$current_unix_time = time();

$content_ids_arr = ContentUtils::getPublishedContentsIdsArrByType($content_type, $limit_to_page, $page);

foreach ($content_ids_arr as $content_id) {
    $content_obj = Content::factory($content_id);

    echo PhpRender::renderTemplateBySkifModule(
        'Content',
        'content_in_list.tpl.php',
        array('content_id' => $content_id)
    );
}

$count_all_articles = ContentUtils::getCountPublishedContentsByType($content_type);
echo Pager::renderPagination($page, $count_all_articles, $limit_to_page);
