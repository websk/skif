<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\PageRegionService;
use WebSK\Skif\Content\TemplateService;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

class BlockSearchHandler extends BaseHandler
{
    use CurrentTemplateIdTrait;

    /** @Inject */
    protected BlockService $block_service;

    /** @Inject */
    protected PageRegionService $page_region_service;

    /** @Inject */
    protected TemplateService $template_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $search_value = $request->getParam('search', '');

        $template_id = $this->getCurrentTemplateId();

        if ((mb_strlen($search_value) > 3)) {
            $blocks_ids_arr = $this->block_service->getIdsArrByPartBody($search_value, $template_id);

            if (count($blocks_ids_arr) == 0) {
                Messages::SetWarning('Ничего не найдено');
            }
        } else {
            Messages::SetWarning('Слишком короткий запрос');
        }

        $content_html = PhpRender::renderTemplateInViewsDir(
            'search_blocks.tpl.php',
            [
                'block_ids_arr' => $blocks_ids_arr ?? [],
                'block_service' => $this->block_service,
                'page_region_service' => $this->page_region_service,
                'template_service' => $this->template_service
            ]
        );

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Поиск блоков');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO('Блоки', $this->urlFor(BlockListHandler::class)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}