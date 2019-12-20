<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Image\ImageController;
use WebSK\Skif\Content\ContentPhoto;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class AdminContentPhotoCreateHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminContentPhotoCreateHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param string $content_type
     * @param int $content_id
     * @return Response
     */
    public function __invoke(Request $request, Response $response, string $content_type, int $content_id)
    {
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
