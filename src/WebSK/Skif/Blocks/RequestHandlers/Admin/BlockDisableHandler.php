<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\PageRegion;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

class BlockDisableHandler extends BaseHandler
{

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
        $block_obj = $this->block_service->getById($block_id);

        if ($block_obj->getPageRegionId() == PageRegion::BLOCK_REGION_NONE) {
            return $response;
        }

        $prev_page_region_id = $block_obj->getPageRegionId();
        $prev_weight = $block_obj->getWeight();

        $restore_url = $this->urlFor(
            BlockChangePositionInRegionHandler::class,
            ['block_id' => $block_id],
            ['target_region' => $prev_page_region_id, 'target_weight' => $prev_weight]
        );

        $this->block_service->disableBlock($block_obj);

        Messages::setWarning('Блок &laquo;' . $block_obj->getTitle() . '&raquo; был выключен. <a href="' . $restore_url . '">Отменить</a>');

        return $response->withHeader('Location', $this->urlFor(BlockListHandler::class))
            ->withStatus(307);
    }
}