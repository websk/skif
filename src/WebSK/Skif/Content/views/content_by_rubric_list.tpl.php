<?php
/**
 * @var $rubric_id
 */

use WebSK\Skif\Pager;
use WebSK\Skif\PhpRender;
use WebSK\Slim\ConfWrapper;
use WebSK\Skif\Content\Content;
use WebSK\Skif\Content\ContentType;
use WebSK\Skif\Content\ContentUtils;
use WebSK\Skif\Content\Rubric;

$rubric_obj = Rubric::factory($rubric_id);

$content_type_obj = ContentType::factory($rubric_obj->getContentTypeId());

$page = array_key_exists('p', $_GET) ? $_GET['p'] : 1;
$limit_to_page = ConfWrapper::value('content.' . $content_type_obj->getType() . '.limit_to_page', 10);
$current_date = date('Y-m-d');
$current_unix_time = time();

$content_ids_arr = ContentUtils::getPublishedContentsIdsArrByRubricId($rubric_id, $limit_to_page, $page);

foreach ($content_ids_arr as $content_id) {
    $content_obj = Content::factory($content_id);

    if (!$content_obj->isPublished()) {
        continue;
    }

    echo PhpRender::renderTemplateBySkifModule(
        'Content',
        'content_in_list.tpl.php',
        array('content_id' => $content_id)
    );
}

$count_all_articles = ContentUtils::getCountPublishedContentsByRubricId($rubric_id);
echo Pager::renderPagination($page, $count_all_articles, $limit_to_page);
