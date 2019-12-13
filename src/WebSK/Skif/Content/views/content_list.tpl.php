<?php
/**
 * @var $content_type
 */

use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\Pager;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\Content\Content;
use WebSK\Slim\Container;
use WebSK\Views\PhpRender;

$page = array_key_exists('p', $_GET) ? $_GET['p'] : 1;
$limit_to_page = ConfWrapper::value('content.' . $content_type . '.limit_to_page');
$current_date = date('Y-m-d');
$current_unix_time = time();

$content_service = ContentServiceProvider::getContentService(Container::self());

$content_ids_arr = $content_service->getPublishedIdsArrByType($content_type, $limit_to_page, $page);

foreach ($content_ids_arr as $content_id) {
    $content_obj = Content::factory($content_id);

    echo PhpRender::renderLocalTemplate(
        'content_in_list.tpl.php',
        array('content_id' => $content_id)
    );
}

$count_all_articles = $content_service->getCountPublishedContentsByType($content_type);
echo Pager::renderPagination($page, $count_all_articles, $limit_to_page);
