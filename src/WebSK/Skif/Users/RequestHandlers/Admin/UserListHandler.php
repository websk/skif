<?php

namespace WebSK\Skif\Users\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Views\LayoutDTO;
use WebSK\Skif\SkifPhpRender;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;

/**
 * Class UserListHandler
 * @package WebSK\Skif\Users\RequestHandlers\Admin
 */
class UserListHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response)
    {
        $content = SkifPhpRender::renderTemplateBySkifModule(
            'Users',
            'users_list.tpl.php'
        );

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Пользователи');
        $layout_dto->setContentHtml($content);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', '/admin'),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return SkifPhpRender::renderLayout($response, $layout_dto);
    }
}
