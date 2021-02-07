<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use WebSK\Image\ImageConstants;
use WebSK\Image\ImageController;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class AdminContentUploadImageAction
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminContentUploadImageAction extends BaseHandler
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

        if (!array_key_exists('image_file', $_FILES) || empty($_FILES['image_file']['name'])) {
            return $response->withStatus(StatusCode::HTTP_NOT_FOUND);
        }

        $root_images_folder = ImageConstants::IMG_ROOT_FOLDER;
        $file = $_FILES['image_file'];
        $file_name = ImageController::processUpload($file, 'content/' . $content_type, $root_images_folder);

        $content_obj->setImage($file_name);
        $content_service->save($content_obj);

        $data = 'OK';

        return $response->withJson($data);
    }
}
