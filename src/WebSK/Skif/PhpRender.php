<?php

namespace WebSK\Skif;

use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;
use WebSK\Views\ViewsPath;

/**
 * Class PhpRender
 * @package WebSK\Skif
 */
class PhpRender
{
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
        $data['response'] = $response;

        $view_path = Path::getSkifViewsPath();

        $relative_to_site_views_file_path = self::getRelativeToRootSiteTemplatePath($template);
        if (file_exists($relative_to_site_views_file_path)) {
            $view_path = Path::getSiteViewsPath();
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
        $relative_to_site_views_file_path = self::getRelativeToRootSiteTemplatePath($template);
        if (file_exists($relative_to_site_views_file_path)) {
            return self::renderTemplateByRelativeToRootSitePath($template, $variables);
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
     * @param string $template
     * @param array $variables
     * @return string
     */
    protected static function renderTemplateByRelativeToRootSitePath(string $template, array $variables = array())
    {
        extract($variables, EXTR_SKIP);
        ob_start();

        require self::getRelativeToRootSiteTemplatePath($template);

        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    /**
     * @param string $template
     * @return string
     */
    protected static function getRelativeToRootSiteTemplatePath(string $template)
    {
        return Path::getSiteViewsPath() . DIRECTORY_SEPARATOR . $template;
    }

    /**
     * @param $module
     * @param $template_file
     * @return bool
     */
    public static function existsTemplateBySkifModuleRelativeToRootSitePath($module, $template_file)
    {
        $relative_to_root_site_file_path = Path::getSiteViewsPath() . DIRECTORY_SEPARATOR . Path::VIEWS_MODULES_DIR . DIRECTORY_SEPARATOR . Path::WEBSK_SKIF_NAMESPACE_DIR . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;

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
            $relative_to_root_site_file_path = Path::VIEWS_MODULES_DIR . DIRECTORY_SEPARATOR . Path::WEBSK_SKIF_NAMESPACE_DIR . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;

            return self::renderTemplateByRelativeToRootSitePath($relative_to_root_site_file_path, $variables);
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
        if (file_exists(Path::getSiteModulesViewsPath() . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file)) {
            return self::renderTemplateByRelativeToRootSitePath(
                Path::VIEWS_MODULES_DIR . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file,
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
}
