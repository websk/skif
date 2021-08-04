<?php

namespace WebSK\Skif\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Auth\Auth;
use WebSK\Config\ConfWrapper;
use WebSK\Skif\SkifPath;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class AdminHandler
 * @package WebSK\Skif\RequestHandlers
 */
class AdminHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        if (!Auth::getCurrentUserId()) {
            $layout_dto = new LayoutDTO();
            $layout_dto->setTitle('СКИФ - Система управления сайтом');

            return PhpRender::renderLayout($response, ConfWrapper::value('layout.login'), $layout_dto);
        }

        return $response->withRedirect(SkifPath::getMainPage());
    }
}
