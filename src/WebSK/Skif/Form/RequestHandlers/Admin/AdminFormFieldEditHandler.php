<?php

namespace WebSK\Skif\Form\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetOptions;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetRadios;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetReferenceAjax;
use WebSK\Logger\LoggerRender;
use WebSK\Skif\Form\Form;
use WebSK\Skif\Form\FormField;
use WebSK\Skif\Form\FormFieldService;
use WebSK\Skif\Form\FormService;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\NavTabItemDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminFormFieldEditHandler
 * @package WebSK\Skif\Form\RequestHandlers\Admin
 */
class AdminFormFieldEditHandler extends BaseHandler
{
    /** @Inject */
    protected FormService $form_service;

    /** @Inject */
    protected FormFieldService $form_field_service;

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $form_field_id
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $form_field_id): ResponseInterface
    {
        $form_field_obj = $this->form_field_service->getById($form_field_id, false);
        if (!$form_field_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $crud_form = $this->crud_service->createForm(
            'form_field_edit',
            $form_field_obj,
            [
                new CRUDFormRow(
                    'Форма',
                    new CRUDFormWidgetReferenceAjax(
                        FormField::_FORM_ID,
                        Form::class,
                        Form::_TITLE,
                        $this->urlFor(AdminFormListAjaxHandler::class),
                        $this->urlFor(
                            AdminFormEditHandler::class,
                            ['form_id' => CRUDFormWidgetReferenceAjax::REFERENCED_ID_PLACEHOLDER]
                        )
                    )
                ),
                new CRUDFormRow('Название', new CRUDFormWidgetInput(FormField::_NAME, false, true)),
                new CRUDFormRow('Комментарий', new CRUDFormWidgetInput(FormField::_COMMENT)),
                new CRUDFormRow(
                    'Тип',
                    new CRUDFormWidgetOptions(FormField::_TYPE, FormField::FIELD_TYPES_ARR, false, true)
                ),
                new CRUDFormRow('Обязательность', new CRUDFormWidgetRadios(FormField::_REQUIRED, [0 => 'Нет', 1 => 'Да'])),
                new CRUDFormRow('Сортировка', new CRUDFormWidgetInput(FormField::_WEIGHT)),
                new CRUDFormRow('Размер', new CRUDFormWidgetInput(FormField::_SIZE, true)),
            ]
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();

        $form_obj = $this->form_service->getById($form_field_obj->getFormId(), false);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($form_field_obj->getName());
        $layout_dto->setContentHtml($content_html);

        $layout_dto->setNavTabsDtoArr(
            [
                new NavTabItemDTO(
                    'Редактирование',
                    $this->urlFor(
                        AdminFormFieldEditHandler::class,
                        ['form_field_id' => $form_field_id]
                    )
                ),
                new NavTabItemDTO('Журнал', LoggerRender::getLoggerLinkForEntityObj($form_field_obj), '_blank'),
            ]
        );

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO('Формы', $this->urlFor(AdminFormListHandler::class)),
            new BreadcrumbItemDTO($form_obj->getTitle(), $this->urlFor(AdminFormEditHandler::class, ['form_id' => $form_field_obj->getFormId()])),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);


        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
