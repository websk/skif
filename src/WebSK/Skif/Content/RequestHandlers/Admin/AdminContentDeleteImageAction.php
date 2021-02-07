<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class AdminContentDeleteImageAction
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminContentDeleteImageAction extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param string $content_type
     * @param int $content_id
     */
    public function __invoke(Request $request, Response $response, string $content_type, int $content_id)
    {
        $content_service = ContentServiceProvider::getContentService($this->container);
        $content_obj = $content_service->getById($content_id, false);

        if (!$content_obj) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        $content_service->deleteImage($content_obj);

        $data = 'OK';

        return $response->withJson($data);
    }
}
