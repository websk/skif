<?php

namespace WebSK\Skif\Form\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\CRUD\CRUD;
use WebSK\CRUD\Form\CRUDFormRow;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetInput;
use WebSK\CRUD\Form\Widgets\CRUDFormWidgetTextarea;
use WebSK\CRUD\Table\CRUDTableColumn;
use WebSK\CRUD\Table\Filters\CRUDTableFilterEqualInline;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetDelete;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetHtml;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetText;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTextWithLink;
use WebSK\CRUD\Table\Widgets\CRUDTableWidgetTimestamp;
use WebSK\Skif\Form\Form;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminFormListHandler
 * @package WebSK\Skif\Form\RequestHandlers\Admin
 */
class AdminFormListHandler extends BaseHandler
{
    const string FILTER_TITLE = 'form_title';

    /** @Inject */
    protected CRUD $crud_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $crud_table_obj = $this->crud_service->createTable(
            Form::class,
            $this->crud_service->createForm(
                'form_create',
                new Form(),
                [
                    new CRUDFormRow('Название формы', new CRUDFormWidgetInput(Form::_TITLE)),
                    new CRUDFormRow('Комментарий', new CRUDFormWidgetInput(Form::_COMMENT)),
                    new CRUDFormRow('Надпись на кнопке', new CRUDFormWidgetInput(Form::_BUTTON_LABEL)),
                    new CRUDFormRow('E-mail', new CRUDFormWidgetInput(Form::_EMAIL)),
                    new CRUDFormRow('Копия на E-mail', new CRUDFormWidgetInput(Form::_EMAIL_COPY)),
                    new CRUDFormRow('Текст письма', new CRUDFormWidgetTextarea(Form::_RESPONSE_MAIL_MESSAGE)),
                    new CRUDFormRow('Адрес страницы', new CRUDFormWidgetInput(Form::_URL))
                ]
            ),
            [
                new CRUDTableColumn('ID', new CRUDTableWidgetText(Form::_ID)),
                new CRUDTableColumn(
                    'Заголовок',
                    new CRUDTableWidgetTextWithLink(
                        Form::_TITLE,
                        function (Form $form) {
                            return $this->urlFor(AdminFormEditHandler::class, ['form_id' => $form->getId()]);
                        }
                    )
                ),
                new CRUDTableColumn(
                    'Email',
                    new CRUDTableWidgetText(
                        Form::_EMAIL
                    )
                ),
                new CRUDTableColumn(
                    'Создан',
                    new CRUDTableWidgetTimestamp(Form::_CREATED_AT_TS)
                ),
                new CRUDTableColumn(
                    'Ссылка',
                    new CRUDTableWidgetHtml(
                        function (Form $form) {
                            return '<a href="' . $form->getUrl() . '" target="_blank">' . $form->getUrl() . '</a>';
                        }
                    )
                ),
                new CRUDTableColumn('', new CRUDTableWidgetDelete())
            ],
            [
                new CRUDTableFilterEqualInline(self::FILTER_TITLE, 'Заголовок', Form::_TITLE),
            ],
            Form::_CREATED_AT_TS . ' DESC'
        );

        $crud_form_response = $crud_table_obj->processRequest($request, $response);
        if ($crud_form_response instanceof ResponseInterface) {
            return $crud_form_response;
        }

        $content_html = $crud_table_obj->html($request);

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Формы');
        $layout_dto->setContentHtml($content_html);
        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}
