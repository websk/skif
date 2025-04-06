<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

class BlockChangePositionInRegionHandler extends BaseHandler
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
        $block_obj = $this->block_service->getById($block_id, false);

        if (!$block_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $target_weight = $request->getParam('target_weight');
        $target_region_id = $request->getParam('target_region') ?: null;

        if ($target_weight == '') {
            return $response;
        }

        $this->block_service->changePositionInRegion($block_obj, $target_weight, $target_region_id);

        Messages::setMessage('Блок &laquo;' . $block_obj->getTitle() . '&raquo; перемещен');

        return $response->withHeader('Location', BlockEditorPositionInRegionHandler::class);
    }
}