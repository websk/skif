<?php

namespace WebSK\Skif\Form;

use WebSK\SimpleRouter\SimpleRouter;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\BaseController;
use WebSK\Slim\Container;
use WebSK\Utils\Exits;
use WebSK\Views\PhpRender;

/**
 * Class FormController
 * @package WebSK\Skif\Form
 */
class FormController extends BaseController
{
    /**
     * @var string
     */
    protected $url_table = Form::DB_TABLE_NAME;

    public function viewAction()
    {
        $form_id = $this->getRequestedId();

        if (!$form_id) {
            return SimpleRouter::CONTINUE_ROUTING;
        }

        $container = Container::self();

        $form_obj = FormServiceProvider::getFormService($container)
            ->getById($form_id, false);

        Exits::exit404If(!$form_obj);

        $content = PhpRender::renderTemplateForModuleNamespace(
            'WebSK/Skif/Form',
            'view.tpl.php',
            array('form_id' => $form_id)
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
