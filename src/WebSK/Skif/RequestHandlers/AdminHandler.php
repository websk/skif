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
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (!Auth::getCurrentUserId()) {
            $layout_dto = new LayoutDTO();
            $layout_dto->setTitle('СКИФ - Система управления сайтом');

            return PhpRender::renderLayout(
                $response,
                ConfWrapper::value('layout.login', '/var/www/views/layouts/layout.admin_login.tpl.php'),
                $layout_dto
            );
        }

        return $response->withHeader('Location', SkifPath::getMainPage());
    }
}
