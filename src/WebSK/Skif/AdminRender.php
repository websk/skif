<?php

namespace WebSK\Skif;

use Psr\Http\Message\ResponseInterface;
use WebSK\Skif\Auth\Auth;
use WebSK\Skif\Users\UsersServiceProvider;
use Websk\Slim\Container;
use WebSK\Slim\Request;
use WebSK\Views\LayoutDTO;

/**
 * Class AdminRender
 * @package WebSK\Skif
 */
class AdminRender
{
    const ADMIN_LAYOUT_TEMPLATE = '/layouts/layout.admin.tpl.php';

    /**
     * @param ResponseInterface $response
     * @param LayoutDTO $layout_dto
     * @return ResponseInterface
     */
    public static function renderLayout(ResponseInterface $response, LayoutDTO $layout_dto)
    {
        $container = Container::self();

        if (!$layout_dto->getSiteTitle()) {
            $layout_dto->setSiteTitle($container['settings']['site_title'] ?? '');
        }

        if (!$layout_dto->getShortSiteTitle()) {
            $short_site_title = $container['settings']['short_site_title'] ?? mb_substr($layout_dto->getSiteTitle(), 0, 3);
            $layout_dto->setShortSiteTitle($short_site_title);
        }

        if (!$layout_dto->getUserName()) {
            $current_user_id = Auth::getCurrentUserId();

            if ($current_user_id) {
                $container = Container::self();
                $user_service = UsersServiceProvider::getUserService($container);
                $current_user = $user_service->getById($current_user_id);

                $layout_dto->setUserId($current_user->getId());
                $layout_dto->setUserName($current_user->getName());
            }
        }

        if (!$layout_dto->getPageUrl()) {
            $layout_dto->setPageUrl(Request::self()->getUri()->getPath());
        }

        $data['layout_dto'] = $layout_dto;

        return PhpRender::render($response, self::ADMIN_LAYOUT_TEMPLATE, $data);
    }
}
