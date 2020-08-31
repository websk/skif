<?php

namespace WebSK\Skif\Content\RequestHandlers;

use WebSK\Auth\Auth;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentEditHandler;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Slim\Router;
use WebSK\Utils\Exits;
use WebSK\Utils\Url;
use WebSK\Views\PhpRender;
use WebSK\Views\ViewsPath;

/**
 * Class ContentViewHandler
 * @package WebSK\Skif\Content\RequestHandlers
 */
class ContentViewHandler extends BaseHandler
{

    public function viewAction()
    {
        $content_service = ContentServiceProvider::getContentService($this->container);

        $current_url = Url::getUriNoQueryString();

        $content_id = $content_service->getIdByUrl($current_url);

        if (!$content_id) {
            return SimpleRouter::CONTINUE_ROUTING;
        }

        $content_obj = $content_service->getById($content_id);
        if (!$content_obj) {
            Exits::exit404();
        }

        if (!$content_obj->isPublished()) {
            Exits::exit404If(!Auth::currentUserIsAdmin());
        }

        $content_type_id = $content_obj->getContentTypeId();

        Exits::exit404If(!$content_type_id);

        $content_type_service = ContentServiceProvider::getContentTypeService($this->container);
        $content_type_obj = $content_type_service->getById($content_type_id);

        $rubric_service = ContentServiceProvider::getRubricService($this->container);

        $content_type = $content_type_obj->getType();

        $content = '';

        $editor_nav_arr = [];
        if (Auth::currentUserIsAdmin()) {
            $editor_nav_arr = [
                Router::pathFor(
                    AdminContentEditHandler::class,
                    ['content_type' => $content_type, 'content_id' => $content_id]
                ) => 'Редактировать'
            ];
        }

        $breadcrumbs_arr = [];

        $main_rubric_id = $content_obj->getMainRubricId();

        if ($main_rubric_id) {
            $main_rubric_obj = $rubric_service->getById($main_rubric_id);

            $breadcrumbs_arr = [$main_rubric_obj->getName() => $main_rubric_obj->getUrl()];
        }


        $template_file = 'content_view.tpl.php';

        if (ViewsPath::existsTemplateByModuleRelativeToRootSitePath(
            'WebSK/Skif/Content',
            'content_' . $content_type . '_view.tpl.php'
        )) {
            $template_file = 'content_' . $content_type . '_view.tpl.php';
        }

        $content_rubric_service = ContentServiceProvider::getContentRubricService($this->container);

        if (is_numeric($content_id) && $content_rubric_service->getCountRubricIdsArrByContentId($content_id)) {
            if (ViewsPath::existsTemplateByModuleRelativeToRootSitePath(
                'WebSK/Skif/Content',
                'content_by_rubric_' . $main_rubric_id . '_view.tpl.php'
            )) {
                $template_file = 'content_by_rubric_' . $main_rubric_id . '_view.tpl.php';
            } else {
                if (ViewsPath::existsTemplateByModuleRelativeToRootSitePath(
                    'WebSK/Skif/Content',
                    'content_by_rubric_view.tpl.php'
                )) {
                    $template_file = 'content_by_rubric_view.tpl.php';
                }
            }
        }

        $content .= PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Content',
            $template_file,
            array('content_id' => $content_id)
        );

        $template_id = $content_service->getRelativeTemplateId($content_obj);

        $template_service = ContentServiceProvider::getTemplateService($this->container);
        $layout_template_file = $template_service->getLayoutFileByTemplateId($template_id);

        echo PhpRender::renderTemplate(
            $layout_template_file,
            [
                'content' => $content,
                'editor_nav_arr' => $editor_nav_arr,
                'title' => $content_obj->getTitle(),
                'keywords' => $content_obj->getKeywords(),
                'description' => $content_obj->getDescription() ?: $content_obj->getTitle(),
                'breadcrumbs_arr' => $breadcrumbs_arr
            ]
        );
    }
}
