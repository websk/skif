<?php

namespace WebSK\Skif\Content\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Image\ImageController;
use WebSK\Skif\Content\ContentPhoto;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\HTTP;

/**
 * Class ContentPhotoCreateHandler
 * @package WebSK\Skif\Content\RequestHandlers
 */
class ContentPhotoCreateHandler extends BaseHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $content_type
     * @param int $content_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $content_type, int $content_id)
    {
        $content_service = ContentServiceProvider::getContentService($this->container);
        $content_obj = $content_service->getById($content_id, false);

        if (!$content_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

        $json = ImageController::processUploadImage();
        $json_arr = json_decode($json, true);

        $file_name = $json_arr['files'][0]['name'];

        $content_photo_service = ContentServiceProvider::getContentPhotoService($this->container);

        $content_photo_obj = new ContentPhoto();
        $content_photo_obj->setContentId($content_id);
        $content_photo_obj->setPhoto($file_name);
        $content_photo_service->save($content_photo_obj);

        $response = $response->withJson($json_arr);

        return $response;
    }
}
