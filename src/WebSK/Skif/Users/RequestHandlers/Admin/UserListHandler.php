<?php

namespace WebSK\Skif\Users\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\AdminRender;
use WebSK\UI\LayoutDTO;
use WebSK\Skif\PhpRender;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\UI\BreadcrumbItemDTO;

/**
 * Class UserListHandler
 * @package WebSK\WebSK\Skif\Users\RequestHandlers\Admin
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
        $content = PhpRender::renderTemplateBySkifModule(
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

        return AdminRender::renderLayout($response, $layout_dto);
    }
}
