<?php

namespace WebSK\Skif\Content\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Content\ContentPhotoService;
use WebSK\Skif\Content\ContentService;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class ContentPhotoSetDefaultHandler
 * @package WebSK\Skif\Content\RequestHandlers
 */
class ContentPhotoSetDefaultHandler extends BaseHandler
{
    /** @Inject */
    protected ContentService $content_service;

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

        $content_photo_ids_arr = $this->content_photo_service->getIdsArrByContentId($content_photo_obj->getContentId());

        foreach ($content_photo_ids_arr as $other_content_photo_id) {
            $other_content_photo_obj = $this->content_photo_service->getById($other_content_photo_id);

            $other_content_photo_obj->setIsDefault(false);
            $this->content_photo_service->save($other_content_photo_obj);
        }

        $content_photo_obj->setIsDefault(true);
        $this->content_photo_service->save($content_photo_obj);

        $json_arr['status'] = 'success';

        return $response->withJson($json_arr);
    }
}
