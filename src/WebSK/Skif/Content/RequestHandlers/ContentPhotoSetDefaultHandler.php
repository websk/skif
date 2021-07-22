<?php

namespace WebSK\Skif\Content\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\HTTP;

/**
 * Class ContentPhotoSetDefaultHandler
 * @package WebSK\Skif\Content\RequestHandlers
 */
class ContentPhotoSetDefaultHandler extends BaseHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $content_photo_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $content_photo_id)
    {
        $content_photo_service = ContentServiceProvider::getContentPhotoService($this->container);

        $content_photo_obj = $content_photo_service->getById($content_photo_id, false);

        if (!$content_photo_obj) {
            return $response->withStatus(HTTP::STATUS_NOT_FOUND);
        }

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
