<?php

namespace WebSK\Skif\Form\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormInvisibleRow;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetOptions;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetRadios;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTextarea;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInvisible;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetCheckbox;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTimestamp;
use WebSK\Logger\LoggerRender;
use WebSK\Skif\Form\Form;
use WebSK\Skif\Form\FormField;
use WebSK\Skif\Form\FormService;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\NavTabItemDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminFormEditHandler
 * @package WebSK\Skif\Form\RequestHandlers\Admin
 */
class AdminFormEditHandler extends BaseHandler
{
    const string FILTER_NAME_FORM_ID = 'form_id';

    /** @Inject */
    protected FormService $form_service;

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $form_id
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $form_id): ResponseInterface
    {
        $form_obj = $this->form_service->getById($form_id, false);

        if (!$form_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $crud_form = $this->crud_service->createForm(
            'form_edit',
            $form_obj,
            [
                new CRUDFormRow('Название формы', new CRUDFormWidgetInput(Form::_TITLE)),
                new CRUDFormRow('Комментарий', new CRUDFormWidgetTextarea(Form::_COMMENT)),
                new CRUDFormRow('Надпись на кнопке', new CRUDFormWidgetInput(Form::_BUTTON_LABEL)),
                new CRUDFormRow('E-mail', new CRUDFormWidgetInput(Form::_EMAIL)),
                new CRUDFormRow('Копию отправлять на E-mail', new CRUDFormWidgetInput(Form::_EMAIL_COPY)),
                new CRUDFormRow('Текст письма в ответ', new CRUDFormWidgetTextarea(Form::_RESPONSE_MAIL_MESSAGE)),
                new CRUDFormRow('Адрес страницы', new CRUDFormWidgetInput(Form::_URL))
            ]
        );

        $crud_form_response = $crud_form->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_form->html();

        $new_form_field_obj = new FormField();
        $new_form_field_obj->setFormId($form_id);

        $crud_table_obj = $this->crud_service->createTable(
            FormField::class,
            $this->crud_service->createForm(
                'form_field_create',
                $new_form_field_obj,
                [
                    new CRUDFormRow('Название', new CRUDFormWidgetInput(FormField::_NAME, false, true)),
                    new CRUDFormRow('Комментарий', new CRUDFormWidgetInput(FormField::_COMMENT)),
                    new CRUDFormRow(
                        'Тип',
                        new CRUDFormWidgetOptions(FormField::_TYPE, FormField::FIELD_TYPES_ARR, false, true)
                    ),
                    new CRUDFormRow('Обязательность', new CRUDFormWidgetRadios(FormField::_REQUIRED, [0 => 'Нет', 1 => 'Да'])),
                    new CRUDFormRow('Сортировка', new CRUDFormWidgetInput(FormField::_WEIGHT)),
                    new CRUDFormRow('Размер', new CRUDFormWidgetInput(FormField::_SIZE, true)),
                    new CRUDFormInvisibleRow(new CRUDFormWidgetInput(FormField::_FORM_ID))
                ]
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(FormField::_ID)),
                new CRUDTableColumn(
                    'Название',
                    new CRUDTableWidgetTextWithLink(
                        FormField::_NAME,
                        function (FormField $form_field) {
                            return $this->urlFor(AdminFormFieldEditHandler::class, ['form_field_id' => $form_field->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Обязательность',
                    new CRUDTableWidgetCheckbox(FormField::_REQUIRED)
                ),
                new CRUDTableColumn(
                    'Сортировка',
                    new CRUDTableWidgetText(FormField::_WEIGHT)
                ),
                new CRUDTableColumn(
                    'Создан',
                    new CRUDTableWidgetTimestamp(FormField::_CREATED_AT_TS)
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInvisible(self::FILTER_NAME_FORM_ID, $form_id),
            ],
            FormField::_WEIGHT . ' DESC'
        );

        $crud_form_table_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_table_response instanceof ResponseInterface) {
            return $crud_form_table_response;
        }

        $content_html .= '<h3>Поля формы</h3>';
        $content_html .= $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($form_obj->getTitle());
        $layout_dto->setContentHtml($content_html);

        $layout_dto->setNavTabsDtoArr(
            [
                new NavTabItemDTO(
                    'Редактирование',
                    $this->urlFor(
                        AdminFormEditHandler::class,
                        ['form_id' => $form_id]
                    )
                ),
                new NavTabItemDTO('Журнал', LoggerRender::getLoggerLinkForEntityObj($form_obj), '_blank'),
            ]
        );

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO('Формы', $this->urlFor(AdminFormListHandler::class)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);


        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
