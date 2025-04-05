<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\PageRegionService;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

class BlockListHandler extends BaseHandler
{

    /** @Inject */
    protected BlockService $block_service;

    /** @Inject */
    protected PageRegionService $page_region_service;


    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $content_html = PhpRender::renderTemplateInViewsDir(
            'blocks_list.tpl.php',
            [
                'block_service' => $this->block_service,
                'page_region_service' => $this->page_region_service
            ]
        );

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle('Блоки');
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }
}