<?php

namespace WebSK\Views;

use Psr\Http\Message\ResponseInterface;
use Slim\Views\PhpRenderer;
use Websk\Utils\Assert;

/**
 * Class PhpRender
 * @package WebSK\Views
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

        $view_path = ViewsPath::getSiteViewsPath();

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

        $full_template_path = $caller_dir . DIRECTORY_SEPARATOR . ViewsPath::VIEWS_DIR_NAME;

        $data['response'] = $response;

        $php_renderer = new PhpRenderer($full_template_path);

        return $php_renderer->render($response, $template, $data);
    }

    /**
     * @param string $template
     * @param array $variables
     * @return string
     */
    public static function renderTemplateByRelativeToRootSitePath(string $template, array $variables = array())
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
    public static function getRelativeToRootSiteTemplatePath(string $template)
    {
        return ViewsPath::getSiteViewsPath() . DIRECTORY_SEPARATOR . $template;
    }
}
