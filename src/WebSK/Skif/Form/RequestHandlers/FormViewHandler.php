<?php

namespace WebSK\Skif\Form\RequestHandlers;

use WebSK\Auth\Auth;
use WebSK\Captcha\CaptchaRoutes;
use WebSK\Config\ConfWrapper;
use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Skif\Form\FormServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Exits;
use WebSK\Utils\Url;
use WebSK\Views\PhpRender;

/**
 * Class FormViewHandler
 * @package WebSK\Skif\Form\RequestHandlers
 */
class FormViewHandler extends BaseHandler
{
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
                'form_send_url' => $this->pathFor(FormSendHandler::class, ['form_id' => $form_id]),
                'captcha_url' => $this->pathFor(CaptchaRoutes::ROUTE_NAME_CAPTCHA_GENERATE),
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
