<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\Skif\Content\ContentRoutes;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\Content\Template;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\HTTP;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminTemplateEditHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminTemplateEditHandler extends BaseHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $template_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $template_id)
    {
        $template_obj = ContentServiceProvider::getTemplateService($this->container)
            ->getById($template_id, false);

        if (!$template_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $crud_form = CRUDServiceProvider::getCrud($this->container)->createForm(
            'template_edit',
            $template_obj,
            [
                new CRUDFormRow('Название', new CRUDFormWidgetInput(Template::_TITLE)),
                new CRUDFormRow('Обозначение', new CRUDFormWidgetInput(Template::_NAME)),
                new CRUDFormRow('Файл CSS', new CRUDFormWidgetInput(Template::_CSS)),
                new CRUDFormRow('Файл шаблона', new CRUDFormWidgetInput(Template::_LAYOUT_TEMPLATE_FILE)),
            ]
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($template_obj->getTitle());
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO('Темы', $this->pathFor(ContentRoutes::ROUTE_NAME_ADMIN_TEMPLATE_LIST)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);


        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }

}
