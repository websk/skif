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
     * Id материалов
     * @param $content_type
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public static function getContentsIdsArrByType($content_type, $limit_to_page = 0, $page = 0)
    {
        $content_type_obj = \Skif\Content\ContentTypeFactory::loadContentTypeByType($content_type);

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
        $content_type_obj = \Skif\Content\ContentTypeFactory::loadContentTypeByType($content_type);

        $query = "SELECT count(id) FROM " . \Skif\Content\Content::DB_TABLE_NAME . " WHERE content_type_id=?";
        return \Skif\DB\DBWrapper::readField($query, array($content_type_obj->getId()));
    }

    public static function getContentsIdsArrByRubricId($rubric_id, $limit_to_page = 0, $page = 0)
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

    public static function getCountContentsByRubricId($rubric_id)
    {
        $query = "SELECT count(id) FROM " . \Skif\Content\ContentRubrics::DB_TABLE_NAME . " WHERE rubric_id=?";
        return \Skif\DB\DBWrapper::readField($query, array($rubric_id));
    }

    /**
     * Id опубликованных материалов
     * @param $content_type
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public static function getPublishedContentsIdsArrByType($content_type, $limit_to_page = 0, $page = 0)
    {
        $date = date('Y-m-d');

        $query = "SELECT id FROM " . \Skif\Content\Content::DB_TABLE_NAME . "
            WHERE type=:content_type AND is_published=1
              AND (published_at<=:date)
              AND (unpublished_at>=:date OR unpublished_at IS NULL)
            ORDER BY created_at DESC";

        if ($limit_to_page) {
            $start_record = $limit_to_page * ($page - 1);
            $query .= " LIMIT " . $start_record . ', ' . $limit_to_page;
        }

        return \Skif\DB\DBWrapper::readColumn($query, array(':content_type' => $content_type, ':date' => $date));
    }

    /**
     * Количество опубликованных материалов
     * @param $content_type
     * @return int
     */
    public static function getCountPublishedContentsByType($content_type)
    {
        $date = date('Y-m-d');

        $query = "SELECT count(id)
            FROM " . \Skif\Content\Content::DB_TABLE_NAME . "
            WHERE type=:content_type AND is_published=1
              AND (published_at<=:date)
              AND (unpublished_at>=:date OR unpublished_at IS NULL)";
        return \Skif\DB\DBWrapper::readField($query, array(':content_type' => $content_type, ':date' => $date));
    }

    /**
     * Id опубликованных материалов в рубрике
     * @param $rubric_id
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public static function getPublishedContentsIdsArrByRubricId($rubric_id, $limit_to_page = 0, $page = 0)
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

        return \Skif\DB\DBWrapper::readColumn($query, array(':rubric_id' => $rubric_id, ':date' => $date));
    }

    public static function getCountPublishedContentsByRubricId($rubric_id)
    {
        $date = date('Y-m-d');

        $query = "SELECT count(cr.id) FROM " . \Skif\Content\ContentRubrics::DB_TABLE_NAME . " cr
                JOIN " . \Skif\Content\Content::DB_TABLE_NAME . " c ON (c.id=cr.content_id)
                WHERE cr.rubric_id=:rubric_id
                  AND c.is_published=1
                  AND (c.published_at<=:date)
                  AND (c.unpublished_at>=:date OR c.unpublished_at IS NULL)";

        return \Skif\DB\DBWrapper::readField($query, array(':rubric_id' => $rubric_id, ':date' => $date));
    }

    /**
     * ID последних статей
     * @param $content_type
     * @return array
     */
    public static function getLastContentsIdsArrByContentType($content_type, $limit)
    {
        $date = date('Y-m-d');

        $query = "SELECT id
            FROM " . \Skif\Content\Content::DB_TABLE_NAME . "
            WHERE type=:content_type AND is_published=1
              AND (published_at<=:date)
              AND (unpublished_at>=:date OR unpublished_at IS NULL)
            ORDER BY created_at DESC LIMIT " . $limit;
        return \Skif\DB\DBWrapper::readColumn($query, array(':content_type' => $content_type, ':date' => $date, ':limit' => $limit));
    }

    public static function renderLastContentsBlock($content_type, $limit = 10, $template = '')
    {
        $contents_ids_arr = \Skif\Content\ContentUtils::getLastContentsIdsArrByContentType($content_type, $limit);

        if (!$template) {
            return \Skif\PhpTemplate::renderTemplateBySkifModule(
                'Content',
                'content_' . $content_type . '_last.tpl.php',
                array('contents_ids_arr' => $contents_ids_arr)
            );
        }

        return \Skif\PhpTemplate::renderTemplate($template, array('contents_ids_arr' => $contents_ids_arr));
    }

    /**
     * Список ID шаблонов
     * @return array
     */
    public static function getTemplatesIdsArr()
    {
        $query = "SELECT id FROM " . \Skif\Content\Template::DB_TABLE_NAME;
        return \Skif\DB\DBWrapper::readColumn($query);
    }

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