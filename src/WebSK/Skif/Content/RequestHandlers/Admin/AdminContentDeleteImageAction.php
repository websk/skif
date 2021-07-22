<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\HTTP;

/**
 * Class AdminContentDeleteImageAction
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminContentDeleteImageAction extends BaseHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $content_type
     * @param int $content_id
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $content_type, int $content_id)
    {
        $content_service = ContentServiceProvider::getContentService($this->container);
        $content_obj = $content_service->getById($content_id, false);

        if (!$content_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $content_service->deleteImage($content_obj);

        $data = 'OK';

        return $response->withJson($data);
    }
}
