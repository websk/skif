<?php

namespace WebSK\Skif\Redirect\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetOptions;
use WebSK\Logger\LoggerRender;
use WebSK\Skif\Redirect\Redirect;
use WebSK\Skif\Redirect\RedirectService;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\NavTabItemDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminRedirectEditHandler
 * @package WebSK\Skif\Redirect\RequestHandlers\Admin
 */
class AdminRedirectEditHandler extends BaseHandler
{
    /** @Inject */
    protected RedirectService $redirect_service;

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $redirect_id
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $redirect_id): ResponseInterface
    {
        $redirect_obj = $this->redirect_service->getById($redirect_id, false);
        if (!$redirect_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $crud_form = $this->crud_service->createForm(
            'redirect_edit',
            $redirect_obj,
            [
                new CRUDFormRow('Исходный URL', new CRUDFormWidgetInput(Redirect::_SRC)),
                new CRUDFormRow('Назначение', new CRUDFormWidgetInput(Redirect::_DST)),
                new CRUDFormRow('HTTP код', new CRUDFormWidgetInput(Redirect::_CODE)),
                new CRUDFormRow('Вид', new CRUDFormWidgetOptions(Redirect::_KIND, Redirect::REDIRECT_KINDS_ARR))
            ]
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Редирект ' . $redirect_obj->getId());
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO('Редиректы', $this->urlFor(AdminRedirectListHandler::class)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        $layout_dto->setNavTabsDtoArr(
            [
                new NavTabItemDTO(
                    'Редактирование',
                    $this->urlFor(
                        self::class,
                        ['redirect_id' => $redirect_id]
                    )
                ),
                new NavTabItemDTO('Журнал', LoggerRender::getLoggerLinkForEntityObj($redirect_obj), '_blank'),
            ]
        );

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
