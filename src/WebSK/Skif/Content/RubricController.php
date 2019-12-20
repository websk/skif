<?php

namespace WebSK\Skif\Content;

use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\BaseController;
use WebSK\Slim\Container;
use WebSK\Views\PhpRender;
use WebSK\Views\ViewsPath;

/**
 * Class RubricController
 * @package WebSK\Skif\Content
 */
class RubricController extends BaseController
{
    protected $url_table = "rubrics";

    /**
     * Список материалов в рубрике
     * @return string
     * @throws \Exception
     */
    public function listAction()
    {
        $requested_id = $this->getRequestedId();

        if (!$requested_id) {
            return SimpleRouter::CONTINUE_ROUTING;
        }

        $rubric_id = $requested_id;

        $container = Container::self();
        $rubric_service = ContentServiceProvider::getRubricService($container);

        $rubric_obj = $rubric_service->getById($rubric_id);

        $content_type_service = ContentServiceProvider::getContentTypeService(Container::self());
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

        $template_service = ContentServiceProvider::getTemplateService(Container::self());
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
