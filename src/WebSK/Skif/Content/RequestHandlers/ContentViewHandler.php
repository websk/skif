<?php

namespace WebSK\Skif\Content\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Auth\Auth;
use WebSK\Skif\Content\Content;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\Content\RequestHandlers\Admin\AdminContentEditHandler;
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
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response, $content_url = null)
    {
        $content_service = ContentServiceProvider::getContentService($this->container);

        //$content_url = Url::appendLeadingSlash($content_url);
        $content_url = Url::getUriNoQueryString();

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


        $content_type_service = ContentServiceProvider::getContentTypeService($this->container);

        $content_type_obj = $content_type_service->getById($content_type_id);
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

        $content_rubric_service = ContentServiceProvider::getContentRubricService($this->container);

        if ($content_rubric_service->getCountRubricIdsArrByContentId($content_id)) {
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


        $template_id = $content_service->getRelativeTemplateId($content_obj);

        $template_service = ContentServiceProvider::getTemplateService($this->container);
        $layout_template_file = $template_service->getLayoutFileByTemplateId($template_id);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($content_obj->getTitle());
        $layout_dto->setKeywords($content_obj->getKeywords());
        $layout_dto->setDescription($content_obj->getDescription());
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', '/')
        ];
        if ($main_rubric_id) {
            $rubric_service = ContentServiceProvider::getRubricService($this->container);
            $main_rubric_obj = $rubric_service->getById($main_rubric_id);

            $breadcrumbs_arr[] = new BreadcrumbItemDTO($main_rubric_obj->getName(), $main_rubric_obj->getUrl());
        }

        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        $nav_bars_dto_arr = [];
        if (Auth::currentUserIsAdmin()) {
            $nav_bars_dto_arr[] = new NavTabItemDTO(
                'Редактировать',
                $this->pathFor(
                    AdminContentEditHandler::class,
                    ['content_type' => $content_type, 'content_id' => $content_id]
                )
            );
        }
        $layout_dto->setNavTabsDtoArr($nav_bars_dto_arr);


        return PhpRender::renderLayout($response, $layout_template_file, $layout_dto);
    }
}
