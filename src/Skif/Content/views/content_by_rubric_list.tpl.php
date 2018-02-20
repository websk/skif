<?php
/**
 * @var $rubric_id
 */

use Skif\Conf\ConfWrapper;
use Skif\Content\Content;
use Skif\Content\ContentType;
use Skif\Content\ContentUtils;
use Skif\Content\Rubric;
use Skif\PhpTemplate;
use Skif\Utils;

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

    echo PhpTemplate::renderTemplateBySkifModule(
        'Content',
        'content_in_list.tpl.php',
        array('content_id' => $content_id)
    );
}

$count_all_articles = ContentUtils::getCountPublishedContentsByRubricId($rubric_id);
echo Utils::renderPagination($page, $count_all_articles, $limit_to_page);
