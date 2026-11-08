<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Blocks\BlockService;
use WebSK\Skif\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

class BlockSaveCachingHandler extends BaseHandler
{
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
        $block_obj = $this->block_service->getById($block_id);

        $block_obj->setCache(\WebSK\CRUD\RequestParams::parsedBody($request, 'cache'));
        $this->block_service->save($block_obj);

        Messages::setMessage('Изменения сохранены');

        return $response->withHeader('Location', $this->urlFor(BlockEditorCachingHandler::class, ['block_id' => $block_id]))
            ->withStatus(StatusCodeInterface::STATUS_TEMPORARY_REDIRECT);
    }
}