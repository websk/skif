<?php

namespace WebSK\Skif\Form\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\Auth;
use WebSK\Captcha\CaptchaRoutes;
use WebSK\Config\ConfWrapper;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Form\FormService;
use WebSK\Skif\Form\FormServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Exits;
use WebSK\Utils\Url;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class FormViewHandler
 * @package WebSK\Skif\Form\RequestHandlers
 */
class FormViewHandler extends BaseHandler
{
    /** @Inject */
    protected FormService $form_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $form_url): ResponseInterface
    {
        $form_url = Url::appendLeadingSlash($form_url);

        $form_id = $this->form_service->getIdByUrl($form_url);

        if (!$form_id) {
            return $response;
        }

        $form_obj = $this->form_service->getById($form_id, false);

        if (!$form_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $form_field_service = FormServiceProvider::getFormFieldService($this->container);

        $content_html = PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Form',
            'view.tpl.php',
            [
                'form_obj' => $form_obj,
                'form_field_service' => $form_field_service,
                'form_send_url' => $this->urlFor(FormSendHandler::class, ['form_id' => $form_id]),
                'captcha_url' => $this->urlFor(CaptchaRoutes::ROUTE_NAME_CAPTCHA_GENERATE),
                'current_user_obj' => Auth::getCurrentUserObj()
            ]
        );

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($form_obj->getTitle());
        $layout_dto->setContentHtml($content_html);

        return PhpRender::renderLayout($response, ConfWrapper::value('layout.main'), $layout_dto);
    }


    public function viewAction()
    {
        $form_service = FormServiceProvider::getFormService($this->container);

        $current_url = Url::getUriNoQueryString();

        $form_id = $form_service->getIdByUrl($current_url);

        if (!$form_id) {
            return SimpleRouter::CONTINUE_ROUTING;
        }

        $form_obj = $form_service->getById($form_id, false);

        Exits::exit404If(!$form_obj);

        $form_field_service = FormServiceProvider::getFormFieldService($this->container);

        $content = PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Form',
            'view.tpl.php',
            [
                'form_obj' => $form_obj,
                'form_field_service' => $form_field_service,
                'form_send_url' => $this->urlFor(FormSendHandler::class, ['form_id' => $form_id]),
                'captcha_url' => $this->urlFor(CaptchaRoutes::ROUTE_NAME_CAPTCHA_GENERATE),
                'current_user_obj' => Auth::getCurrentUserObj()
            ]
        );

        echo PhpRender::renderTemplate(
            ConfWrapper::value('layout.main'),
            array(
                'title' => $form_obj->getTitle(),
                'content' => $content,
            )
        );
    }
}
