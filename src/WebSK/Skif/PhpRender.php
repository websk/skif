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
    const VIEWS_DIR = 'views';
    const VIEWS_RELATIVE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . self::VIEWS_DIR;

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

        $php_renderer = new PhpRenderer(realpath(self::VIEWS_RELATIVE_PATH));

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

        $full_template_path = $caller_dir . DIRECTORY_SEPARATOR . self::VIEWS_DIR;

        $data['response'] = $response;

        $php_renderer = new PhpRenderer($full_template_path);

        return $php_renderer->render($response, $template, $data);
    }
}
