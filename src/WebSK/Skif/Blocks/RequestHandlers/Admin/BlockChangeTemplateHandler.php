<?php

namespace WebSK\Skif\Blocks\RequestHandlers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Slim\RequestHandlers\BaseHandler;
use WebSK\Utils\Messages;

class BlockChangeTemplateHandler extends BaseHandler
{
    use CurrentTemplateIdTrait;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param int $template_id
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, int $template_id): ResponseInterface
    {
        $this->setCurrentTemplateId($template_id);

        Messages::setMessage('Тема изменена');

        return $response->withHeader('Location', $this->urlFor(BlockListHandler::class));
    }
}