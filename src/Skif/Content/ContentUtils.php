<?php

namespace Skif\Content;


class ContentUtils
{

    public static function getCurrentContentId()
    {
        $content_page_obj = new \Skif\Content\ContentController();
        $content_id = $content_page_obj->getRequestedId();

        if (!$content_id) {
            return 0;
        }

        $content_obj = \Skif\Content\Content::factory($content_id, false);
        if (!$content_obj) {
            return 0;
        }

        return $content_id;
    }

    public static function getCurrentRubricId()
    {
        $content_page_obj = new \Skif\Content\RubricController();
        $rubric_id = $content_page_obj->getRequestedId();

        if (!$rubric_id) {
            return 0;
        }

        $rubric_obj = \Skif\Content\Rubric::factory($rubric_id, false);
        if (!$rubric_obj) {
            return 0;
        }

        return $rubric_id;
    }

    /**
     * Массив ID материалов
     * @param $content_type
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public static function getContentsIdsArrByType($content_type, $limit_to_page = 0, $page = 1)
    {
        $content_type_obj = \Skif\Content\ContentType::factoryByFieldsArr(array('type' => $content_type));

        $query = "SELECT id FROM " . \Skif\Content\Content::DB_TABLE_NAME . " WHERE content_type_id=? ORDER BY created_at DESC";
        $param_arr = array($content_type_obj->getId());

        if ($limit_to_page) {
            $start_record = $limit_to_page * ($page - 1);
            $query .= " LIMIT " . $start_record . ', ' . $limit_to_page;
        }

        return \Skif\DB\DBWrapper::readColumn($query, $param_arr);
    }

    public static function getCountContentsByType($content_type)
    {
        $contents_ids_arr = \Skif\Content\ContentUtils::getContentsIdsArrByType($content_type);

        $count_contents = count($contents_ids_arr);

        return $count_contents;
    }

    /**
     * Массив ID материалов в рубрике
     * @param $rubric_id
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public static function getContentsIdsArrByRubricId($rubric_id, $limit_to_page = 0, $page = 1)
    {
        $query = "SELECT cr.content_id FROM " . \Skif\Content\ContentRubrics::DB_TABLE_NAME . " cr
                JOIN " . \Skif\Content\Content::DB_TABLE_NAME . " c ON (c.id=cr.content_id)
                WHERE cr.rubric_id=? ORDER BY c.created_at DESC";
        $param_arr = array($rubric_id);

        if ($limit_to_page) {
            $start_record = $limit_to_page * ($page - 1);
            $query .= " LIMIT " . $start_record . ', ' . $limit_to_page;
        }

        return \Skif\DB\DBWrapper::readColumn($query, $param_arr);
    }

    /**
     * Количество материалов в рубрике
     * @param $rubric_id
     * @return int
     */
    public static function getCountContentsByRubricId($rubric_id)
    {
        $contents_ids_arr = \Skif\Content\ContentUtils::getContentsIdsArrByRubricId($rubric_id);

        $count_contents = count($contents_ids_arr);

        return $count_contents;
    }

    /**
     * Id опубликованных материалов
     * @param $content_type
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public static function getPublishedContentsIdsArrByType($content_type, $limit_to_page = 0, $page = 1)
    {
        $date = date('Y-m-d');

        $content_type_obj = \Skif\Content\ContentType::factoryByFieldsArr(array('type' => $content_type));

        $query = "SELECT id FROM " . \Skif\Content\Content::DB_TABLE_NAME . "
            WHERE content_type_id=:content_type_id AND is_published=1
              AND (published_at<=:date)
              AND (unpublished_at>=:date OR unpublished_at IS NULL)
            ORDER BY created_at DESC";

        if ($limit_to_page) {
            $start_record = $limit_to_page * ($page - 1);
            $query .= " LIMIT " . $start_record . ', ' . $limit_to_page;
        }

        $contents_ids_arr = \Skif\DB\DBWrapper::readColumn($query, array(':content_type_id' => $content_type_obj->getId(), ':date' => $date));

        return $contents_ids_arr;
    }

    /**
     * Количество опубликованных материалов
     * @param $content_type
     * @return int
     */
    public static function getCountPublishedContentsByType($content_type)
    {
        $contents_ids_arr = \Skif\Content\ContentUtils::getPublishedContentsIdsArrByType($content_type);

        $count_contents = count($contents_ids_arr);

        return $count_contents;
    }

    /**
     * Id опубликованных материалов в рубрике
     * @param $rubric_id
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public static function getPublishedContentsIdsArrByRubricId($rubric_id, $limit_to_page = 0, $page = 1)
    {
        $date = date('Y-m-d');

        $query = "SELECT cr.content_id FROM " . \Skif\Content\ContentRubrics::DB_TABLE_NAME . " cr
                JOIN " . \Skif\Content\Content::DB_TABLE_NAME . " c ON (c.id=cr.content_id)
                WHERE cr.rubric_id=:rubric_id
                  AND c.is_published=1
                  AND (c.published_at<=:date)
                  AND (c.unpublished_at>=:date OR c.unpublished_at IS NULL)
                ORDER BY c.created_at DESC";

        if ($limit_to_page) {
            $start_record = $limit_to_page * ($page - 1);
            $query .= " LIMIT " . $start_record . ', ' . $limit_to_page;
        }

        $contents_ids_arr = \Skif\DB\DBWrapper::readColumn($query, array(':rubric_id' => $rubric_id, ':date' => $date));

        return $contents_ids_arr;
    }

    /**
     * Количество опубликованных материалов в рубрике
     * @param $rubric_id
     * @return int
     */
    public static function getCountPublishedContentsByRubricId($rubric_id)
    {
        $contents_ids_arr = \Skif\Content\ContentUtils::getPublishedContentsIdsArrByRubricId($rubric_id);

        $count_contents = count($contents_ids_arr);

        return $count_contents;
    }

    /**
     * Блок последних материалов
     * @param $content_type
     * @param int $limit
     * @param string $template
     * @return string
     */
    public static function renderLastContentsBlock($content_type, $limit = 10, $template = '')
    {
        $contents_ids_arr = \Skif\Content\ContentUtils::getPublishedContentsIdsArrByType($content_type, $limit);

        if (!$template) {
            $template_file = 'content_last_list.tpl.php';

            if (\Skif\PhpTemplate::existsTemplateBySkifModuleRelativeToRootSitePath('Content', 'content_' . $content_type . '_last_list.tpl.php')) {
                $template_file = 'content_' . $content_type . '_last_list.tpl.php';
            }

            return \Skif\PhpTemplate::renderTemplateBySkifModule(
                'Content',
                $template_file,
                array('contents_ids_arr' => $contents_ids_arr)
            );
        }

        return \Skif\PhpTemplate::renderTemplate($template, array('contents_ids_arr' => $contents_ids_arr));
    }

    /**
     * Блок последних материалов в рубрике
     * @param $rubric_id
     * @param int $limit
     * @param string $template
     * @return string
     */
    public static function renderLastContentsBlockByRubric($rubric_id, $limit = 10, $template = '')
    {
        $contents_ids_arr = \Skif\Content\ContentUtils::getPublishedContentsIdsArrByRubricId($rubric_id, $limit);

        if (!$template) {
            $template_file = 'content_last_list.tpl.php';

            if (\Skif\PhpTemplate::existsTemplateBySkifModuleRelativeToRootSitePath('Content', 'content_by_rubric_' . $rubric_id . '_last_list.tpl.php')) {
                $template_file = 'content_by_rubric_' . $rubric_id . '_last_list.tpl.php';
            }

            return \Skif\PhpTemplate::renderTemplateBySkifModule(
                'Content',
                $template_file,
                array('contents_ids_arr' => $contents_ids_arr)
            );
        }

        return \Skif\PhpTemplate::renderTemplate($template, array('contents_ids_arr' => $contents_ids_arr));
    }

    /**
     * Фильтрация html тегов
     * @param $content
     * @return string
     */
    public static function filterContent($content)
    {
        $allowable_tags_arr = array(
            '<p>',
            '<b><strong><em><i>',
            '<span>',
            '<br>',
            '<div>',
            '<a>',
            '<img>',
            '<h1><h2><h3><h4>',
            '<table><tr><td><tbody><thead><th>',
            '<li><ul><ol>',
            '<script>',
            '<hr>',
            '<form><input><iframe>'
        );

        return strip_tags($content, implode('', $allowable_tags_arr));
    }
}