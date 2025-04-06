<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

class BlockDeleteHandler extends BaseHandler
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

        $block_name = $block_obj->getTitle();

        $this->block_service->delete($block_obj);

        Messages::setMessage('Блок &laquo;' . $block_name . '&raquo; удален');

        return $response->withHeader('Location', $this->urlFor(BlockListHandler::class));
    }
}