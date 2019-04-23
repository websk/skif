<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\PhpRender;

/**
 * Class ContentPhotoListHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class ContentPhotoListHandler extends BaseHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @param string $content_type
     * @param int $content_id
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response, string $content_type, int $content_id)
    {
        return PhpRender::render(
            $response,
            __DIR__ . '/../../views/content_photo_form_edit_photo_list.tpl.php',
            ['content_id' => $content_id]
        );
    }
}
