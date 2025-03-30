<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTextarea;
use WebSK\Skif\Content\ContentTypeService;
use WebSK\Skif\Content\Rubric;
use WebSK\Skif\Content\RubricService;
use WebSK\Skif\Content\Template;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminRubricEditHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminRubricEditHandler extends BaseHandler
{
    /** @Inject */
    protected ContentTypeService $content_type_service;

    /** @Inject */
    protected RubricService $rubric_service;

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $content_type
     * @param int $rubric_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $content_type, int $rubric_id): ResponseInterface
    {
        $content_type_obj = $this->content_type_service->getByType($content_type);
        if (!$content_type_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $rubric_obj = $this->rubric_service->getById($rubric_id, false);
        if (!$rubric_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $crud_form = $this->crud_service->createForm(
            'rubric_edit',
            $rubric_obj,
            [
                new CRUDFormRow('Название', new CRUDFormWidgetInput(Rubric::_NAME)),
                new CRUDFormRow('Комментарий', new CRUDFormWidgetTextarea(Rubric::_COMMENT)),
                new CRUDFormRow('URL', new CRUDFormWidgetInput(Rubric::_URL)),
                new CRUDFormRow(
                    'Шаблон',
                    new CRUDFormWidgetReferenceAjax(
                        Rubric::_TEMPLATE_ID,
                        Template::class,
                        Template::_TITLE,
                        $this->urlFor(AdminTemplateListAjaxHandler::class),
                        $this->urlFor(
                            AdminTemplateEditHandler::class,
                            ['template_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER]
                        )
                    )
                ),
            ]
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($content_type_obj->getName());
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO($content_type_obj->getName(), $this->urlFor(AdminContentTypeListHandler::class, ['content_type' => $content_type])),
            new BreadcrumbItemDTO('Рубрики', $this->urlFor(AdminRubricListHandler::class, ['content_type' => $content_type])),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
