<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Blocks\PageRegionService;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

class BlockEditorPositionInRegionHandler extends BaseHandler
{
    use BlockEditorPageTitleTrait;

    /** @Inject */
    protected PageRegionService $page_region_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $block_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $block_id): ResponseInterface
    {
        $block_obj = $this->block_service->getById($block_id, false);

        if (!$block_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $target_region_id = $request->getParam('target_region') ?: $block_obj->getPageRegionId();

        $content_html = PhpRender::renderTemplateInViewsDir(
            'block_position_in_region.tpl.php',
            [
                'block_id' => $block_id,
                'target_region' => $target_region_id,
                'block_service' => $this->block_service,
                'page_region_service' => $this->page_region_service
            ]
        );

        $layout_dto = new LayoutDTO();
        $layout_dto->setTitle($this->getBlockEditorPageTitle($block_id));
        $layout_dto->setContentHtml($content_html);

        $breadcrumbs_arr = [
            new BreadcrumbItemDTO('Главная', SkifPath::getMainPage()),
            new BreadcrumbItemDTO('Блоки', $this->urlFor(BlockListHandler::class)),
        ];
        $layout_dto->setBreadcrumbsDtoArr($breadcrumbs_arr);

        return PhpRender::renderLayout($response, SkifPath::getLayout(), $layout_dto);
    }

}