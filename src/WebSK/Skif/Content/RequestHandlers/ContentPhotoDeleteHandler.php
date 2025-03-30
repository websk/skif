<?php

namespace WebSK\Skif\Content\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Image\ImageManager;
use WebSK\Skif\Content\ContentPhotoService;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class ContentPhotoDeleteHandler
 * @package WebSK\Skif\Content\RequestHandlers
 */
class ContentPhotoDeleteHandler extends BaseHandler
{

    /** @Inject */
    protected ContentPhotoService $content_photo_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $content_photo_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $content_photo_id): ResponseInterface
    {
        $content_photo_obj = $this->content_photo_service->getById($content_photo_id, false);

        if (!$content_photo_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $image_manager = new ImageManager();
        $image_manager->removeImageFile($content_photo_obj->getPhotoPath());

        $this->content_photo_service->delete($content_photo_obj);

        $json_arr['status'] = 'success';

        return $response->withJson($json_arr);
    }
}
