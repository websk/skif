<?php

namespace WebSK\Skif\Content\RequestHandlers;

use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Url;
use WebSK\Views\PhpRender;
use WebSK\Views\ViewsPath;

/**
 * Class ContentInRubricListHandler
 * @package WebSK\Skif\Content\RequestHandlers
 */
class ContentInRubricListHandler extends BaseHandler
{
    public function listAction()
    {
        $rubric_service = ContentServiceProvider::getRubricService($this->container);

        $current_url = Url::getUriNoQueryString();

        $rubric_id = $rubric_service->getIdbyUrl($current_url);

        if (!$rubric_id) {
            return SimpleRouter::CONTINUE_ROUTING;
        }

        $rubric_obj = $rubric_service->getById($rubric_id);

        $content_type_service = ContentServiceProvider::getContentTypeService($this->container);
        $content_type_obj = $content_type_service->getById($rubric_obj->getContentTypeId());

        $template_file = 'content_by_rubric_' . $rubric_id . '_list.tpl.php';
        if (!ViewsPath::existsTemplateByModuleRelativeToRootSitePath('WebSK/Skif/Content', $template_file)) {
            $template_file = 'content_' . $content_type_obj->getType() . '_by_rubric_list.tpl.php';
        }
        if (!ViewsPath::existsTemplateByModuleRelativeToRootSitePath('WebSK/Skif/Content', $template_file)) {
            $template_file = 'content_by_rubric_list.tpl.php';
        }

        $content = PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Content',
            $template_file,
            array(
                'rubric_id' => $rubric_id
            )
        );

        $template_id = $rubric_service->getRelativeTemplateId($rubric_obj);

        $template_service = ContentServiceProvider::getTemplateService($this->container);
        $layout_template_file = $template_service->getLayoutFileByTemplateId($template_id);

        echo PhpRender::renderTemplate(
            $layout_template_file,
            array(
                'content' => $content,
                'title' => $rubric_obj->getName(),
                'keywords' => '',
                'description' => ''
            )
        );
    }
}
