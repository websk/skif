<?php
/**
 * @var $rubric_id
 */

$rubric_obj = \Skif\Content\Rubric::factory($rubric_id);

$content_type_obj = \Skif\Content\ContentType::factory($rubric_obj->getContentTypeId());

$page = array_key_exists('p', $_GET) ? $_GET['p'] : 1;
$limit_to_page = \Skif\Conf\ConfWrapper::value('content.' . $content_type_obj->getType() . '.limit_to_page');
$current_date = date('Y-m-d');
$current_unix_time = time();

$content_ids_arr = \Skif\Content\ContentUtils::getPublishedContentsIdsArrByRubricId($rubric_id, $limit_to_page, $page);

foreach ($content_ids_arr as $content_id) {
    $content_obj = \Skif\Content\Content::factory($content_id);

    if (!$content_obj->isPublished()) {
        continue;
    }

    echo \Skif\PhpTemplate::renderTemplateBySkifModule(
        'Content',
        'content_in_list.tpl.php',
        array('content_id' => $content_id)
    );
}

$count_all_articles = \Skif\Content\ContentUtils::getCountPublishedContentsByRubricId($rubric_id);
echo \Skif\Utils::renderPagination($page, $count_all_articles, $limit_to_page);
