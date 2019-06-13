<?php

namespace WebSK\Skif\Content\RequestHandlers;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Auth\Auth;
use WebSK\Skif\Content\Content;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\Content\ContentType;
use WebSK\Skif\Content\RequestHandlers\Admin\ContentEditHandler;
use WebSK\Skif\Content\Rubric;
use WebSK\Skif\Content\TemplateUtils;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Url;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\NavTabItemDTO;
use WebSK\Views\PhpRender;
use WebSK\Views\ViewsPath;

/**
 * Class ContentViewHandler
 * @package WebSK\Skif\Content\RequestHandlers
 */
class ContentViewHandler extends BaseHandler
{

    /**
     * @param Request $request
     * @param Response $response
     * @param string $content_url
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, string $content_url = '/')
    {
        $content_service = ContentServiceProvider::getContentService($this->container);

        $content_url = Url::appendLeadingSlash($content_url);

        $content_id = $content_service->getIdByAlias($content_url);

        if (!$content_id) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        $content_obj = Content::factory($content_id);
        if (!$content_obj) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        if (!$content_obj->isPublished()) {
            if (!Auth::currentUserIsAdmin()) {
                return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
            }
        }

        $content_type_id = $content_obj->getContentTypeId();

        if (!$content_type_id) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }


        $content_type_obj = ContentType::factory($content_type_id);
        $content_type = $content_type_obj->getType();

        $content_html = '';

        $main_rubric_id = $content_obj->getMainRubricId();

        $template_file = 'content_view.tpl.php';

        if (ViewsPath::existsTemplateByModuleRelativeToRootSitePath(
            'WebSK/Skif/Content',
            'content_' . $content_type . '_view.tpl.php'
        )) {
            $template_file = 'content_' . $content_type . '_view.tpl.php';
        }

        if ($content_obj->getCountRubricIdsArr()) {
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

        $content_html .= PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Content',
            $template_file,
            ['content_id' => $content_id]
        );


        $template_id = $content_obj->getRelativeTemplateId();

        $layout_template_file = TemplateUtils::getLayoutFileByTemplateId($template_id);


        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($content_obj->getTitle());
        $layout_dto->setKeywords($content_obj->getKeywords());
        $layout_dto->setDescription($content_obj->getDescription());
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [];
        if ($main_rubric_id) {
            $main_rubric_obj = Rubric::factory($main_rubric_id);

            $breadcrumbs_arr = new BreadcrumbItemDTO($main_rubric_obj->getName(), $main_rubric_obj->getUrl());
        }

        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        $nav_bars_dto_arr = [];
        if (Auth::currentUserIsAdmin()) {
            $nav_bars_dto_arr[] = new NavTabItemDTO(
                'Редактировать',
                $this->pathFor(
                    ContentEditHandler::class,
                    ['content_type' => $content_type, 'content_id' => $content_id]
                )
            );
        }
        $layout_dto->setNavTabsDtoArr($nav_bars_dto_arr);


        return PhpRender::renderLayout($response, $layout_template_file, $layout_dto);
    }
}
