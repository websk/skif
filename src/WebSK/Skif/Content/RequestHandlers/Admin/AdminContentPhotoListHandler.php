<?php

namespace WebSK\Skif\Content\RequestHandlers\Admin;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Views\PhpRender;

/**
 * Class AdminContentPhotoListHandler
 * @package WebSK\Skif\Content\RequestHandlers\Admin
 */
class AdminContentPhotoListHandler extends BaseHandler
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param string $content_type
     * @param int $content_id
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, string $content_type, int $content_id)
    {
        return PhpRender::render(
            $response,
            __DIR__ . '/../../views/content_photo_form_edit_photo_list.tpl.php',
            ['content_id' => $content_id]
        );
    }
}
