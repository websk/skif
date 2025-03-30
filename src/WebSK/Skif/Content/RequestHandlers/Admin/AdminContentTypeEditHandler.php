<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\Skif\Content\ContentType;
use WebSK\Skif\Content\ContentTypeService;
use WebSK\Skif\Content\Template;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminContentTypeEditHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminContentTypeEditHandler extends BaseHandler
{
    /** @Inject */
    protected ContentTypeService $content_type_service;

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $content_type_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $content_type_id): ResponseInterface
    {
        $content_type_obj = $this->content_type_service->getById($content_type_id, false);
        if (!$content_type_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $crud_form = $this->crud_service->createForm(
            'content_type_edit',
            $content_type_obj,
            [
                new CRUDFormRow('Название', new CRUDFormWidgetInput(ContentType::_NAME)),
                new CRUDFormRow('Тип', new CRUDFormWidgetInput(ContentType::_TYPE)),
                new CRUDFormRow('URL', new CRUDFormWidgetInput(ContentType::_URL)),
                new CRUDFormRow(
                    'Шаблон',
                    new CRUDFormWidgetReferenceAjax(
                        ContentType::_TEMPLATE_ID,
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
            new BreadcrumbItemDTO('Типы контента', $this->urlFor(AdminContentTypeListHandler::class)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
