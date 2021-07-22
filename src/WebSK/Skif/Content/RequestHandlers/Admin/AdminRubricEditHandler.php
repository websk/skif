<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUDServiceProvider;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTextarea;
use WebSK\Skif\Content\ContentRoutes;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Skif\Content\Rubric;
use WebSK\Skif\Content\Template;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\HTTP;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminRubricEditHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminRubricEditHandler extends BaseHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $content_type
     * @param int $rubric_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $content_type, int $rubric_id)
    {
        $content_type_obj = ContentServiceProvider::getContentTypeService($this->container)
            ->getByType($content_type);

        if (!$content_type_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $rubric_obj = ContentServiceProvider::getRubricService($this->container)
            ->getById($rubric_id, false);

        if (!$rubric_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $crud_form = CRUDServiceProvider::getCrud($this->container)->createForm(
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
                        $this->pathFor(ContentRoutes::ROUTE_NAME_ADMIN_TEMPLATE_LIST_AJAX),
                        $this->pathFor(
                            ContentRoutes::ROUTE_NAME_ADMIN_TEMPLATE_EDIT,
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
            new BreadcrumbItemDTO($content_type_obj->getName(), '/admin/content/' . $content_type),
            new BreadcrumbItemDTO('Рубрики', $this->pathFor(ContentRoutes::ROUTE_NAME_ADMIN_RUBRIC_LIST, ['content_type' => $content_type])),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
