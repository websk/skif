<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Content\ContentService;
use WebSK\Skif\Content\ContentTypeService;
use WebSK\Skif\Content\RubricService;
use WebSK\Skif\Content\TemplateService;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

class AdminContentEditHandler extends BaseHandler
{
    /** @Inject */
    protected ContentTypeService $content_type_service;

    /** @Inject */
    protected ContentService $content_service;

    /** @Inject */
    protected RubricService $rubric_service;

    /** @Inject */
    protected TemplateService $template_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $content_type
     * @param int $content_id
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $content_type, int $content_id): ResponseInterface
    {
        $content_type_obj = $this->content_type_service->getByType($content_type);

        if (!$content_type_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $content_obj = $this->content_service->getById($content_id, false);
        if (!$content_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $content_html = PhpRender::renderTemplate(
            __DIR__ . '/../../views/admin_content_form_edit.tpl.php',
            [
                'content_id' => $content_id,
                'content_type' => $content_type,
                'content_service' => $this->content_service,
                'content_type_service' => $this->content_type_service,
                'rubric_service' => $this->rubric_service,
                'template_service' => $this->template_service,
            ]
        );

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Редактирование материала');
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO($content_type_obj->getName(), $this->urlFor(AdminContentListHandler::class, ['content_type' => $content_type, 'content_id' => $content_id]))
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
