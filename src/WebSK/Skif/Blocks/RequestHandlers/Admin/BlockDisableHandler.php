<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\Auth;
use WebSK\Logger\Logger;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\Blocks\PageRegion;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\FullObjectId;
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

        $prev_region = $block_obj->getPageRegionId();
        $prev_weight = $block_obj->getWeight();

        $restore_url = $this->urlFor(BlockEditHandler::class, ['block_id' => $block_id]);
        $restore_url .= '/position?_action=move_block&target_region=' . $prev_region . '&target_weight=' . $prev_weight;

        $block_obj->setPageRegionId(PageRegion::BLOCK_REGION_NONE);
        $this->block_service->save($block_obj);

        $this->block_service->clearBlockIdsArrByPageRegionIdCache($prev_region, $block_obj->getTemplateId());
        $this->block_service->clearBlockIdsArrByPageRegionIdCache(PageRegion::BLOCK_REGION_NONE, $block_obj->getTemplateId());

        Logger::logObjectEvent($block_obj, 'отключение', FullObjectId::getFullObjectId(Auth::getCurrentUserObj()));

        Messages::setWarning('Блок &laquo;' . $block_obj->getTitle() . '&raquo; был выключен. <a href="' . $restore_url . '">Отменить</a>');

        return $response->withHeader('Location', $this->urlFor(BlockListHandler::class));
    }
}