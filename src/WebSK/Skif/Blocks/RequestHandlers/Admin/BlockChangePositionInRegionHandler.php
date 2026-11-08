<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

class BlockChangePositionInRegionHandler extends BaseHandler
{
    use BlockEditorPageTitleTrait;

    #[\DI\Attribute\Inject]
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

        $target_weight_param = \WebSK\CRUD\RequestParams::queryString($request, 'target_weight');
        $target_region_param = \WebSK\CRUD\RequestParams::queryString($request, 'target_region');

        if ($target_weight_param === '') {
            return $response;
        }

        if (filter_var($target_region_param, FILTER_VALIDATE_INT) === false) {
            throw new \InvalidArgumentException('Target region must be an integer');
        }
        $target_region_id = (int)$target_region_param;

        if ($target_weight_param === \WebSK\Skif\Blocks\Block::BLOCK_WEIGHT_FIRST_IN_REGION) {
            $target_weight = $target_weight_param;
        } elseif (filter_var($target_weight_param, FILTER_VALIDATE_INT) !== false) {
            $target_weight = (int)$target_weight_param;
        } else {
            throw new \InvalidArgumentException('Target weight must be an integer or FIRST');
        }

        $this->block_service->changePositionInRegion($block_obj, $target_weight, $target_region_id);

        Messages::setMessage('Блок &laquo;' . $block_obj->getTitle() . '&raquo; перемещен');

        return $response->withHeader('Location', $this->urlFor(BlockEditorPositionInRegionHandler::class, ['block_id' => $block_id]))
            ->withStatus(StatusCodeInterface::STATUS_TEMPORARY_REDIRECT);

    }
}
