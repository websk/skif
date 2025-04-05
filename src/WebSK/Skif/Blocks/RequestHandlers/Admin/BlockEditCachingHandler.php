<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\SkifPath;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\BreadcrumbItemDTO;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

class BlockEditCachingHandler extends BaseHandler
{
    use BlockEditorPageTitleTrait;

    /** @Inject */
    protected BlockService $block_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $block_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $block_id): ResponseInterface
    {
        $content_html = PhpRender::renderTemplateInViewsDir(
            'block_caching.tpl.php',
            [
                'block_id' => $block_id,
                'block_service' => $this->block_service
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