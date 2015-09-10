<?php

namespace Skif\Content;


class ContentUtils
{

    /**
     * Id материалов
     * @param $content_type
     * @param int $limit_to_page
     * @param int $page
     * @return array
     */
    public static function getContentsIdsArrByType($content_type, $limit_to_page = 0, $page = 0)
    {
        $query = "SELECT id FROM content WHERE type=? ORDER BY created_at DESC";

        if ($limit_to_page) {
            $start_record = $limit_to_page * ($page - 1);
            $query .= " LIMIT " . $start_record . ', ' . $limit_to_page;
        }

        return \Skif\DB\DBWrapper::readColumn($query, array($content_type));
    }

    public static function getCountContentsByType($content_type)
    {
        $query = "SELECT count(id) FROM content WHERE type=?";
        return \Skif\DB\DBWrapper::readField($query, array($content_type));
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

        $query = "SELECT id FROM content
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
            FROM content
            WHERE type=:content_type AND is_published=1
              AND (published_at<=:date)
              AND (unpublished_at>=:date OR unpublished_at IS NULL)";
        return \Skif\DB\DBWrapper::readField($query, array(':content_type' => $content_type, ':date' => $date));
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
            FROM content
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

        return \Skif\PhpTemplate::renderTemplateRelativeToRootSitePath($template, array('contents_ids_arr' => $contents_ids_arr));
    }

    /**
     * Список ID шаблонов
     * @return array
     */
    public static function getTemplatesIdsArr()
    {
        $query = "SELECT id FROM template";
        return \Skif\DB\DBWrapper::readColumn($query);
    }

    public static function filterContent($content)
    {
        return strip_tags($content, "<p><b><strong><em><br><div><a><img><h1><h2><h3><h4><table><tr><td><tbody><thead><th><li><ul><script><hr><form><input><iframe>");
    }
}