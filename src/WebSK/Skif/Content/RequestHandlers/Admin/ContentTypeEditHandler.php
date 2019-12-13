<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\Skif\Content\ContentRoutes;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\Content\ContentType;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class ContentTypeEditHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class ContentTypeEditHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int $content_type_id
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response, int $content_type_id)
    {
        $content_type_obj = ContentServiceProvider::getContentTypeService($this->container)
            ->getById($content_type_id, false);

        if (!$content_type_obj) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        $crud_form = CRUDServiceProvider::getCrud($this->container)->createForm(
            'content_type_edit',
            $content_type_obj,
            [
                new CRUDFormRow('Название', new CRUDFormWidgetInput(ContentType::_NAME)),
                new CRUDFormRow('Тип', new CRUDFormWidgetInput(ContentType::_TYPE)),
                new CRUDFormRow('URL', new CRUDFormWidgetInput(ContentType::_URL)),
                new CRUDFormRow(
                    'Шаблон',
                    new CRUDFormWidgetInput(ContentType::_TEMPLATE_ID)
                ),
            ]
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof Response) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($content_type_obj->getName());
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO('Типы контента', $this->pathFor(ContentRoutes::ROUTE_NAME_ADMIN_CONTENT_TYPE_LIST)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);


        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
