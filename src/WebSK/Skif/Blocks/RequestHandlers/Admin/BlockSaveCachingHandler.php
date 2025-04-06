<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

class BlockSaveCachingHandler extends BaseHandler
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

        $block_obj->setCache($request->getParam('cache'));
        $this->block_service->save($block_obj);

        Messages::setMessage('Изменения сохранены');

        return $response->withHeader('Location', BlockEditorCachingHandler::class);
    }
}