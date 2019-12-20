<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Image\ImageManager;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class AdminContentPhotoDeleteHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminContentPhotoDeleteHandler extends BaseHandler
{

    /**
     * @param Request $request
     * @param Response $response
     * @param int $content_photo_id
     * @return Response
     */
    public function __invoke(Request $request, Response $response, int $content_photo_id)
    {
        $content_photo_service = ContentServiceProvider::getContentPhotoService($this->container);

        $content_photo_obj = $content_photo_service->getById($content_photo_id);

        $image_manager = new ImageManager();
        $image_manager->removeImageFile($content_photo_obj->getPhotoPath());

        $content_photo_service->delete($content_photo_obj);

        $json_arr['status'] = 'success';

        $response = $response->withJson($json_arr);

        return $response;
    }
}
