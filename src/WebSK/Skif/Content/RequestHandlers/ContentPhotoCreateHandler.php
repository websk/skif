<?php

namespace WebSK\Skif\Content\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Image\ImageController;
use WebSK\Skif\Content\ContentPhoto;
use WebSK\Skif\Content\ContentPhotoService;
use WebSK\Skif\Content\ContentService;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class ContentPhotoCreateHandler
 * @package WebSK\Skif\Content\RequestHandlers
 */
class ContentPhotoCreateHandler extends BaseHandler
{
    /** @Inject */
    protected ContentService $content_service;

    /** @Inject */
    protected ContentPhotoService $content_photo_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $content_type
     * @param int $content_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $content_type, int $content_id): ResponseInterface
    {
        $content_obj = $this->content_service->getById($content_id, false);

        if (!$content_obj) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $json = ImageController::processUploadImage();
        $json_arr = json_decode($json, true);

        $file_name = $json_arr['files'][0]['name'];

        $content_photo_obj = new ContentPhoto();
        $content_photo_obj->setContentId($content_id);
        $content_photo_obj->setPhoto($file_name);
        $this->content_photo_service->save($content_photo_obj);

        return $response->withJson($json_arr);
    }
}
