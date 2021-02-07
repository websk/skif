<?php

namespace WebSK\Skif;

use Psr\Http\Message\ResponseInterface;
use WebSK\Auth\Auth;
use WebSK\Auth\User\UserServiceProvider;
use WebSK\Config\ConfWrapper;
use WebSK\Slim\Container;
use WebSK\Slim\Request;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender;

/**
 * Class SkifPhpRender
 * @package WebSK\Skif
 */
class SkifPhpRender
{
    public const ADMIN_LAYOUT_TEMPLATE = '/layouts/layout.admin.tpl.php';
    public const ADMIN_LAYOUT_LOGIN_TEMPLATE = '/layouts/layout.admin_login.tpl.php';

    /**
     * @param ResponseInterface $response
     * @param string $template
     * @param array $data
     * @return ResponseInterface
     */
    public static function render(
        ResponseInterface $response,
        string $template,
        array $data = []
    ) {
        $view_path = SkifPath::getViewsPath();

        $template_path = $view_path . DIRECTORY_SEPARATOR . $template;

        return PhpRender::render($response, $template_path, $data);
    }

    /**
     * @param ResponseInterface $response
     * @param LayoutDTO $layout_dto
     * @return ResponseInterface
     */
    public static function renderLayout(ResponseInterface $response, LayoutDTO $layout_dto)
    {
        if (!$layout_dto->getSiteTitle()) {
            $layout_dto->setSiteTitle(ConfWrapper::value('site_title', ''));
        }

        if (!$layout_dto->getShortSiteTitle()) {
            $short_site_title = ConfWrapper::value('short_site_title', mb_substr($layout_dto->getSiteTitle(), 0, 3));
            $layout_dto->setShortSiteTitle($short_site_title);
        }

        if (!$layout_dto->getUserName()) {
            $current_user_id = Auth::getCurrentUserId();

            if ($current_user_id) {
                $container = Container::self();
                $user_service = UserServiceProvider::getUserService($container);
                $current_user = $user_service->getById($current_user_id);

                $layout_dto->setUserId($current_user->getId());
                $layout_dto->setUserName($current_user->getName());
            }
        }

        if (!$layout_dto->getPageUrl()) {
            $layout_dto->setPageUrl(Request::self()->getUri()->getPath());
        }

        $data['layout_dto'] = $layout_dto;

        return self::render($response, self::ADMIN_LAYOUT_TEMPLATE, $data);
    }
}
