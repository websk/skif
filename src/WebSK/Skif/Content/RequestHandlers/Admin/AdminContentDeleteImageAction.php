<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Content\ContentService;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Slim\Response;

/**
 * Class AdminContentDeleteImageAction
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminContentDeleteImageAction extends BaseHandler
{
    /** @Inject */
    protected ContentService $content_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $content_type
     * @param int $content_id
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $content_type, int $content_id): ResponseInterface
    {
        $content_obj = $this->content_service->getById($content_id, false);

        if (!$content_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $this->content_service->deleteImage($content_obj);

        $data = 'OK';

        return Response::responseWithJson($response, $data);
    }
}
