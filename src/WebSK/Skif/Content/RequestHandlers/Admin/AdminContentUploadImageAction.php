<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Image\ImageConstants;
use WebSK\Image\ImageController;
use WebSK\Skif\Content\ContentService;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Slim\Response;

/**
 * Class AdminContentUploadImageAction
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminContentUploadImageAction extends BaseHandler
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

        if (!array_key_exists('image_file', $_FILES) || empty($_FILES['image_file']['name'])) {
            return $response->withStatus(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $root_images_folder = ImageConstants::IMG_ROOT_FOLDER;
        $file = $_FILES['image_file'];
        $file_name = ImageController::processUpload($file, 'content/' . $content_type, $root_images_folder);

        $content_obj->setImage($file_name);
        $this->content_service->save($content_obj);

        $data = 'OK';

        return Response::responseWithJson($response, $data);
    }
}
