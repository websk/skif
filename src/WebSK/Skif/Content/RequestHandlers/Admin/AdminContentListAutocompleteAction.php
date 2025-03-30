<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Skif\Content\ContentService;
use WebSK\Skif\Content\ContentTypeService;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class AdminContentListAutocompleteAction
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminContentListAutocompleteAction extends BaseHandler
{
    const string PARAM_TERM = 'term';

    /** @Inject */
    protected ContentTypeService $content_type_service;

    /** @Inject */
    protected ContentService $content_service;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $term = $request->getParam(self::PARAM_TERM);

        $content_ids_arr = $this->content_service->getIdsArrByTitle($term);

        $output_arr = [];
        foreach ($content_ids_arr as $content_id) {
            $content_obj = $this->content_service->getById($content_id);

            $content_type_obj = $this->content_type_service->getById($content_obj->getContentTypeId());

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
