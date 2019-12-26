<?php

namespace WebSK\Skif\Content\RequestHandlers;

use WebSK\Config\ConfWrapper;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\PhpRender;
use WebSK\Views\ViewsPath;

/**
 * Class ContentListHandler
 * @package WebSK\Skif\Content\RequestHandlers
 */
class ContentListHandler extends BaseHandler
{
    public function listAction(string $content_type)
    {
        if (!ConfWrapper::value('content.' . $content_type)) {
            return SimpleRouter::CONTINUE_ROUTING;
        }

        $template_file = 'content_list.tpl.php';

        if (ViewsPath::existsTemplateByModuleRelativeToRootSitePath(
            'WebSK/Skif/Content',
            'content_' . $content_type . '_list.tpl.php'
        )) {
            $template_file = 'content_' . $content_type . '_list.tpl.php';
        }

        $content = PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Content',
            $template_file,
            array('content_type' => $content_type)
        );

        $content_type_service = ContentServiceProvider::getContentTypeService($this->container);
        $content_type_obj = $content_type_service->getByType($content_type);

        $template_id = $content_type_obj->getTemplateId();

        $template_service = ContentServiceProvider::getTemplateService($this->container);
        $layout_template_file = $template_service->getLayoutFileByTemplateId($template_id);

        echo PhpRender::renderTemplate(
            $layout_template_file,
            array(
                'content' => $content,
                'title' => $content_type_obj->getName(),
                'keywords' => '',
                'description' => ''
            )
        );
    }
}
