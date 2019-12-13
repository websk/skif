<?php

namespace WebSK\Skif\Content;

use WebSK\DB\DBWrapper;
use WebSK\Slim\Container;
use WebSK\Utils\Url;
use WebSK\Views\PhpRender;
use WebSK\Views\ViewsPath;

/**
 * Class ContentUtils
 * @package WebSK\Skif\Content
 */
class ContentUtils
{
    /**
     * @return int|null
     */
    public static function getCurrentContentId()
    {
        $container = Container::self();

        $content_service = ContentServiceProvider::getContentService($container);

        $content_url = Url::getUriNoQueryString();

        $content_id = $content_service->getIdByAlias($content_url);

        if (!$content_id) {
            return 0;
        }

        $content_obj = Content::factory($content_id, false);
        if (!$content_obj) {
            return 0;
        }

        return $content_id;
    }

    /**
     * @return int|null
     */
    public static function getCurrentRubricId()
    {
        $content_page_obj = new RubricController();
        $rubric_id = $content_page_obj->getRequestedId();

        if (!$rubric_id) {
            return 0;
        }

        $rubric_obj = Rubric::factory($rubric_id, false);
        if (!$rubric_obj) {
            return 0;
        }

        return $rubric_id;
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
        $query = "SELECT cr.content_id FROM " . ContentRubrics::DB_TABLE_NAME . " cr
                JOIN " . Content::DB_TABLE_NAME . " c ON (c.id=cr.content_id)
                WHERE cr.rubric_id=? ORDER BY c.created_at DESC";
        $param_arr = array($rubric_id);

        if ($limit_to_page) {
            $start_record = $limit_to_page * ($page - 1);
            $query .= " LIMIT " . $start_record . ', ' . $limit_to_page;
        }

        return DBWrapper::readColumn($query, $param_arr);
    }

    /**
     * Количество материалов в рубрике
     * @param $rubric_id
     * @return int
     */
    public static function getCountContentsByRubricId($rubric_id)
    {
        $contents_ids_arr = self::getContentsIdsArrByRubricId($rubric_id);

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

        $query = "SELECT cr.content_id FROM " . ContentRubrics::DB_TABLE_NAME . " cr
                JOIN " . Content::DB_TABLE_NAME . " c ON (c.id=cr.content_id)
                WHERE cr.rubric_id=?
                  AND c.is_published=1
                  AND (c.published_at<=?)
                  AND (c.unpublished_at>=? OR c.unpublished_at IS NULL)
                GROUP BY cr.content_id ORDER BY c.created_at DESC";

        if ($limit_to_page) {
            $start_record = $limit_to_page * ($page - 1);
            $query .= " LIMIT " . $start_record . ', ' . $limit_to_page;
        }

        $contents_ids_arr = DBWrapper::readColumn($query, [$rubric_id, $date, $date]);

        return $contents_ids_arr;
    }

    /**
     * Количество опубликованных материалов в рубрике
     * @param $rubric_id
     * @return int
     */
    public static function getCountPublishedContentsByRubricId($rubric_id)
    {
        $contents_ids_arr = self::getPublishedContentsIdsArrByRubricId($rubric_id);

        $count_contents = count($contents_ids_arr);

        return $count_contents;
    }

    /**
     * Блок последних материалов
     * @param $content_type
     * @param int $limit
     * @param string $template_file
     * @return string
     */
    public static function renderLastContentsBlock($content_type, $limit = 10, $template_file = '')
    {
        $content_service = ContentServiceProvider::getContentService(Container::self());

        $contents_ids_arr = $content_service->getPublishedIdsArrByType($content_type, $limit);

        if (!$template_file) {
            $template_file = 'content_last_list.tpl.php';

            if (ViewsPath::existsTemplateByModuleRelativeToRootSitePath(
                'WebSK/Skif/Content',
                'content_' . $content_type . '_last_list.tpl.php'
            )) {
                $template_file = 'content_' . $content_type . '_last_list.tpl.php';
            }

            return PhpRender::renderTemplateForModuleNamespace(
                'WebSK/Skif/Content',
                $template_file,
                array('contents_ids_arr' => $contents_ids_arr)
            );
        }

        return PhpRender::renderTemplate($template_file, array('contents_ids_arr' => $contents_ids_arr));
    }

    /**
     * Блок последних материалов в рубрике
     * @param $rubric_id
     * @param int $limit
     * @param string $template_file
     * @return string
     */
    public static function renderLastContentsBlockByRubricId($rubric_id, $limit = 10, $template_file = '')
    {
        $contents_ids_arr = self::getPublishedContentsIdsArrByRubricId($rubric_id, $limit);

        if (!$template_file) {
            $template_file = 'content_last_list.tpl.php';

            if (ViewsPath::existsTemplateByModuleRelativeToRootSitePath(
                'WebSK/Skif/Content',
                'content_by_rubric_' . $rubric_id . '_last_list.tpl.php'
            )) {
                $template_file = 'content_by_rubric_' . $rubric_id . '_last_list.tpl.php';
            } else {
                $rubric_obj = Rubric::factory($rubric_id);

                $content_type_id = $rubric_obj->getContentTypeId();

                $content_type_service = ContentServiceProvider::getContentTypeService(Container::self());

                $content_type_obj = $content_type_service->getById($content_type_id);
                $content_type = $content_type_obj->getType();

                if (ViewsPath::existsTemplateByModuleRelativeToRootSitePath(
                    'WebSK/Skif/Content',
                    'content_' . $content_type . '_last_list.tpl.php'
                )) {
                    $template_file = 'content_' . $content_type . '_last_list.tpl.php';
                }
            }

            return PhpRender::renderTemplateForModuleNamespace(
                'WebSK/Skif/Content',
                $template_file,
                array('contents_ids_arr' => $contents_ids_arr)
            );
        }

        return PhpRender::renderTemplate($template_file, array('contents_ids_arr' => $contents_ids_arr));
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
