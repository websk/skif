<?php

namespace WebSK\Skif;

use Websk\Utils\Assert;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;

/**
 * Class PhpRender
 * @package WebSK\Skif
 */
class PhpRender
{
    //const VIEWS_RELATIVE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . self::VIEWS_DIR;

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

        $view_path = Path::getSiteViewsPath();
        $php_renderer = new PhpRenderer($view_path);

        return $php_renderer->render($response, $template, $data);
    }

    /**
     * @param ResponseInterface $response
     * @param string $template
     * @param array $data
     * @return ResponseInterface
     */
    public static function renderLocal(
        ResponseInterface $response,
        string $template,
        array $data = []
    ) {
        $cb_arr = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
        $caller_obj = array_shift($cb_arr);
        Assert::assert($caller_obj);

        $caller_path = $caller_obj['file'];
        $caller_path_arr = pathinfo($caller_path);

        $caller_dir = $caller_path_arr['dirname'];

        $full_template_path = $caller_dir . DIRECTORY_SEPARATOR . Path::VIEWS_DIR_NAME;

        $data['response'] = $response;

        $php_renderer = new PhpRenderer($full_template_path);

        return $php_renderer->render($response, $template, $data);
    }

    /**
     * Вывод шаблона относительно views
     * @param $template_file
     * @param array $variables
     * @return string
     */
    public static function renderTemplate($template_file, $variables = [])
    {
        $relative_to_site_views_file_path = Path::getSiteViewsPath() . DIRECTORY_SEPARATOR . $template_file;

        if (file_exists($relative_to_site_views_file_path)) {
            return self::renderTemplateRelativeToRootSitePath($template_file, $variables);
        }

        $relative_to_skif_views_file_path = Path::getSkifViewsPath() . DIRECTORY_SEPARATOR . $template_file;

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
     * @param $template_file
     * @param array $variables
     * @return string
     */
    protected static function renderTemplateRelativeToRootSitePath($template_file, $variables = array())
    {
        extract($variables, EXTR_SKIP);
        ob_start();

        require Path::getSiteViewsPath() . DIRECTORY_SEPARATOR . $template_file;

        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    /**
     * @param $module
     * @param $template_file
     * @return bool
     */
    public static function existsTemplateBySkifModuleRelativeToRootSitePath($module, $template_file)
    {
        $relative_to_root_site_file_path = Path::getSiteViewsPath() . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'WebSK\Skif' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;

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
            $relative_to_root_site_file_path = 'modules' . DIRECTORY_SEPARATOR . 'WebSK\Skif' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;

            return self::renderTemplateRelativeToRootSitePath($relative_to_root_site_file_path, $variables);
        }

        extract($variables, EXTR_SKIP);
        ob_start();

        require Path::getSkifAppPath() . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . Path::VIEWS_DIR_NAME . DIRECTORY_SEPARATOR . $template_file;
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
        if (file_exists(Path::getSiteViewsPath() . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file)) {
            return self::renderTemplateRelativeToRootSitePath('modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file, $variables);
        }

        extract($variables, EXTR_SKIP);
        ob_start();

        require Path::getSiteSrcPath() . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . Path::VIEWS_DIR_NAME . DIRECTORY_SEPARATOR . $template_file;

        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }
}
