<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Content\ContentServiceProvider;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class AdminContentListAutocompleteAction
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminContentListAutocompleteAction extends BaseHandler
{
    const PARAM_TERM = 'term';

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $content_type
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $term = $request->getParam(self::PARAM_TERM);

        $content_service = ContentServiceProvider::getContentService($this->container);
        $content_type_service = ContentServiceProvider::getContentTypeService($this->container);

        $content_ids_arr = $content_service->getIdsArrByTitle($term);

        $output_arr = [];
        foreach ($content_ids_arr as $content_id) {
            $content_obj = $content_service->getById($content_id);

            $content_type_obj = $content_type_service->getById($content_obj->getContentTypeId());

            $output_arr[] = [
                'id' => $content_id,
                'label' => $content_obj->getTitle(),
                'value' => $content_obj->getTitle(),
                'type' => $content_type_obj->getName(),
                'url' => $content_obj->getUrl(),
            ];
        }

        return $response->withJson($output_arr);
    }
}
