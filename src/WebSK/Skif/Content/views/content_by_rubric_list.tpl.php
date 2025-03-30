<?php
/**
 * @var int $rubric_id
 */

use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\Pager;
use WebSK\Config\ConfWrapper;
use WebSK\Slim\Container;
use WebSK\Views\PhpRender;

$rubric_service = ContentServiceProvider::getRubricService(Container::self());
$content_service = ContentServiceProvider::getContentService(Container::self());

$rubric_obj = $rubric_service->getById($rubric_id);

$content_type_service = ContentServiceProvider::getContentTypeService(Container::self());

$content_type_obj = $content_type_service->getById($rubric_obj->getContentTypeId());

$page = array_key_exists('p', $_GET) ? $_GET['p'] : 1;
$limit_to_page = ConfWrapper::value('content.' . $content_type_obj->getType() . '.limit_to_page', 10);
$current_date = date('Y-m-d');
$current_unix_time = time();

$content_rubric_service = ContentServiceProvider::getContentRubricService(Container::self());

$content_ids_arr = $content_rubric_service->getPublishedContentIdsArrByRubricId($rubric_id, $limit_to_page, $page);

foreach ($content_ids_arr as $content_id) {
    $content_obj = $content_service->getById($content_id);

    if (!$content_obj->isPublished()) {
        continue;
    }

    echo PhpRender::renderLocalTemplate(
        'content_in_list.tpl.php',
        array('content_id' => $content_id)
    );
}

$count_all_articles = $content_rubric_service->getCountPublishedContentsByRubricId($rubric_id);
echo Pager::renderPagination($page, $count_all_articles, $limit_to_page);
