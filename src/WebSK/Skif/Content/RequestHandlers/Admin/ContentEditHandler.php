<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Auth\Auth;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\Content\ContentType;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Exits;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class ContentEditHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class ContentEditHandler extends BaseHandler
{

    /**
     * @param Request $request
     * @param Response $response
     * @param string $content_type
     * @param int $content_id
     */
    public function __invoke(Request $request, Response $response, string $content_type, int $content_id)
    {
        // Проверка прав доступа
        Exits::exit403If(!Auth::currentUserIsAdmin());

        $content_html = PhpRender::renderTemplate(
            __DIR__ . '/../../views/admin_content_form_edit.tpl.php',
            ['content_id' => $content_id, 'content_type' => $content_type]
        );

        $content_type_obj = ContentType::factoryByFieldsArr(['type' => $content_type]);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Редактирование материала');
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', ConfWrapper::value('skif_main_page', '/admin')),
            new BreadcrumbItemDTO($content_type_obj->getName(), '/admin/content/' . $content_type)
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.admin'), $layout_dto);
    }
}
