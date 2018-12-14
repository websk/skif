<?php

namespace WebSK\Skif;

use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;
use WebSK\Skif\Auth\Auth;
use WebSK\Skif\Users\UsersServiceProvider;
use WebSK\Config\ConfWrapper;
use WebSK\Slim\Container;
use WebSK\Slim\Request;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender as ViewsPhpRender;
use WebSK\Views\ViewsPath;

/**
 * Class SkifPhpRender
 * @package WebSK\Skif
 */
class SkifPhpRender
{
    public const ADMIN_LAYOUT_TEMPLATE = '/layouts/layout.admin.tpl.php';

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
        $view_path = SkifPath::getSkifViewsPath();

        $relative_to_site_views_file_path = ViewsPath::getFullTemplatePath($template);
        if (file_exists($relative_to_site_views_file_path)) {
            $view_path = ViewsPath::getSiteViewsPath();
        }

        $php_renderer = new PhpRenderer($view_path);

        return $php_renderer->render($response, $template, $data);
    }

    /**
     * Вывод шаблона относительно views
     * @param string $template
     * @param array $variables
     * @return string
     */
    public static function renderTemplate(string $template, array $variables = [])
    {
        $relative_to_site_views_file_path = ViewsPath::getFullTemplatePath($template);
        if (file_exists($relative_to_site_views_file_path)) {
            return ViewsPhpRender::renderTemplate($template, $variables);
        }

        $relative_to_skif_views_file_path = SkifPath::getSkifViewsPath() . DIRECTORY_SEPARATOR . $template;

        if (!file_exists($relative_to_skif_views_file_path)) {
            return '';
        }

        extract($variables, EXTR_SKIP);
        ob_start();

        require $relative_to_skif_views_file_path;

        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    /**
     * @param string $module
     * @param string $template
     * @return string
     */
    protected static function getRelativeToRootSiteSkifModuleViewsPath(string $module, string $template)
    {
        return ViewsPath::getFullTemplatePath(
            ViewsPath::VIEWS_MODULES_DIR . DIRECTORY_SEPARATOR . SkifPath::WEBSK_SKIF_NAMESPACE_DIR . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template
        );
    }

    /**
     * @param string $module
     * @param string $template
     * @return bool
     */
    public static function existsTemplateBySkifModuleRelativeToRootSitePath(string $module, string $template)
    {
        $relative_to_root_site_file_path = self::getRelativeToRootSiteSkifModuleViewsPath($module, $template);

        return file_exists($relative_to_root_site_file_path);
    }

    /**
     * @param $module
     * @param $template
     * @param array $variables
     * @return string
     */
    public static function renderTemplateBySkifModule(string $module, string $template, array $variables = [])
    {
        if (self::existsTemplateBySkifModuleRelativeToRootSitePath($module, $template)) {
            $relative_to_root_site_file_path = self::getRelativeToRootSiteSkifModuleViewsPath($module, $template);

            return ViewsPhpRender::renderTemplate($relative_to_root_site_file_path, $variables);
        }

        extract($variables, EXTR_SKIP);
        ob_start();

        require SkifPath::getSkifAppPath() . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . ViewsPath::VIEWS_DIR_NAME . DIRECTORY_SEPARATOR . $template;
        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
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

        return self::render($response, self::ADMIN_LAYOUT_TEMPLATE, $data);
    }
}
