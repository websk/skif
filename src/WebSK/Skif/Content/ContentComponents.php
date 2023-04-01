<?php

namespace WebSK\Skif\Content;

use WebSK\Slim\Container;
use WebSK\Views\PhpRender;
use WebSK\Views\ViewsPath;

/**
 * Class ContentComponents
 * @package WebSK\Skif\Content
 */
class ContentComponents
{
    /**
     * Блок последних материалов
     * @param string $content_type
     * @param int $limit
     * @param string $template_file
     * @return string
     */
    public static function renderLastContentsBlock(string $content_type, int $limit = 10, string $template_file = ''): string
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
     * @param int $rubric_id
     * @param int $limit
     * @param string $template_file
     * @return string
     */
    public static function renderLastContentsBlockByRubricId(int $rubric_id, int $limit = 10, string $template_file = ''): string
    {
        $content_rubric_service = ContentServiceProvider::getContentRubricService(Container::self());

        $contents_ids_arr = $content_rubric_service->getPublishedContentIdsArrByRubricId($rubric_id, $limit);

        if (!$template_file) {
            $template_file = 'content_last_list.tpl.php';

            if (ViewsPath::existsTemplateByModuleRelativeToRootSitePath(
                'WebSK/Skif/Content',
                'content_by_rubric_' . $rubric_id . '_last_list.tpl.php'
            )) {
                $template_file = 'content_by_rubric_' . $rubric_id . '_last_list.tpl.php';
            } else {
                $rubric_service = ContentServiceProvider::getRubricService(Container::self());

                $rubric_obj = $rubric_service->getById($rubric_id);

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

        return PhpRender::renderTemplate($template_file, ['contents_ids_arr' => $contents_ids_arr]);
    }

}
