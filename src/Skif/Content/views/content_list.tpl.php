<?php
/**
 * @var $content_type
 */

$page = array_key_exists('p', $_GET) ? $_GET['p'] : 1;
$limit_to_page = \Skif\Conf\ConfWrapper::value('content.' . $content_type . '.limit_to_page');
$current_date = date('Y-m-d');
$current_unix_time = time();

$content_ids_arr = \Skif\Content\ContentUtils::getPublishedContentsIdsArrByType($content_type, $limit_to_page, $page);

foreach ($content_ids_arr as $content_id) {
    $content_obj = \Skif\Content\Content::factory($content_id);

    if (!$content_obj->isPublished()) {
        continue;
    }
    /*
    if (strtotime($content_obj->getPublishedAt()) > $current_unix_time) {
        continue;
    }

    if ($content_obj->getUnpublishedAt() && (strtotime($content_obj->getUnpublishedAt()) < $current_unix_time)) {
        continue;
    }
    */
    echo \Skif\PhpTemplate::renderTemplateBySkifModule(
        'Content',
        'content_in_list.tpl.php',
        array('content_id' => $content_id)
    );
}

$count_all_articles = \Skif\Content\ContentUtils::getCountPublishedContentsByType($content_type);
echo \Skif\Utils::renderPagination($page, $count_all_articles, $limit_to_page);
