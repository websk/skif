<?php

namespace WebSK\Skif;

use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;
use WebSK\Skif\Auth\Auth;
use WebSK\Skif\Users\UsersServiceProvider;
use Websk\Slim\Container;
use WebSK\Slim\Request;
use WebSK\Views\LayoutDTO;
use WebSK\Views\PhpRender as ViewsPhpRender;
use WebSK\Views\ViewsPath;

/**
 * Class PhpRender
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
        $view_path = Path::getSkifViewsPath();

        $relative_to_site_views_file_path = ViewsPhpRender::getRelativeToRootSiteTemplatePath($template);
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
        $relative_to_site_views_file_path = ViewsPhpRender::getRelativeToRootSiteTemplatePath($template);
        if (file_exists($relative_to_site_views_file_path)) {
            return ViewsPhpRender::renderTemplateByRelativeToRootSitePath($template, $variables);
        }

        $relative_to_skif_views_file_path = Path::getSkifViewsPath() . DIRECTORY_SEPARATOR . $template;

        if (file_exists($relative_to_skif_views_file_path)) {
            extract($variables, EXTR_SKIP);
            ob_start();

            require $relative_to_skif_views_file_path;

            $contents = ob_get_contents();
            ob_end_clean();

            return $contents;
        }

        return '';
    }

    /**
     * @param $module
     * @param $template_file
     * @return bool
     */
    public static function existsTemplateBySkifModuleRelativeToRootSitePath($module, $template_file)
    {
        $relative_to_root_site_file_path = ViewsPath::getSiteViewsPath() . DIRECTORY_SEPARATOR . ViewsPath::VIEWS_MODULES_DIR . DIRECTORY_SEPARATOR . Path::WEBSK_SKIF_NAMESPACE_DIR . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;

        return file_exists($relative_to_root_site_file_path);
    }

    /**
     * @param $module
     * @param $template_file
     * @param array $variables
     * @return string
     */
    public static function renderTemplateBySkifModule($module, $template_file, $variables = array())
    {
        if (self::existsTemplateBySkifModuleRelativeToRootSitePath($module, $template_file)) {
            $relative_to_root_site_file_path = ViewsPath::VIEWS_MODULES_DIR . DIRECTORY_SEPARATOR . Path::WEBSK_SKIF_NAMESPACE_DIR . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;

            return ViewsPhpRender::renderTemplateByRelativeToRootSitePath($relative_to_root_site_file_path, $variables);
        }

        extract($variables, EXTR_SKIP);
        ob_start();

        require Path::getSkifAppPath() . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . ViewsPath::VIEWS_DIR_NAME . DIRECTORY_SEPARATOR . $template_file;
        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }

    /**
     * @param $module
     * @param $template_file
     * @param array $variables
     * @return string
     */
    public static function renderTemplateByModule($module, $template_file, $variables = array())
    {
        if (file_exists(ViewsPath::getSiteModulesViewsPath() . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file)) {
            return ViewsPhpRender::renderTemplateByRelativeToRootSitePath(
                ViewsPath::VIEWS_MODULES_DIR . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file,
                $variables
            );
        }

        extract($variables, EXTR_SKIP);
        ob_start();

        require Path::getSkifAppPath() . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . ViewsPath::VIEWS_DIR_NAME . DIRECTORY_SEPARATOR . $template_file;

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
        $container = Container::self();

        if (!$layout_dto->getSiteTitle()) {
            $layout_dto->setSiteTitle($container['settings']['site_title'] ?? '');
        }

        if (!$layout_dto->getShortSiteTitle()) {
            $short_site_title = $container['settings']['short_site_title'] ?? mb_substr($layout_dto->getSiteTitle(), 0,3);
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

        return SkifPhpRender::render($response, self::ADMIN_LAYOUT_TEMPLATE, $data);
    }
}
