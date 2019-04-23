<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class SetDefaultContentPhotoHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class SetDefaultContentPhotoHandler extends BaseHandler
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

        $content_photo_ids_arr = $content_photo_service->getIdsArrByContentId($content_photo_obj->getContentId());

        foreach ($content_photo_ids_arr as $other_content_photo_id) {
            $other_content_photo_obj = $content_photo_service->getById($other_content_photo_id);

            $other_content_photo_obj->setIsDefault(false);
            $content_photo_service->save($other_content_photo_obj);
        }

        $content_photo_obj->setIsDefault(true);
        $content_photo_service->save($content_photo_obj);

        $json_arr['status'] = 'success';

        $response = $response->withJson($json_arr);

        return $response;
    }
}
