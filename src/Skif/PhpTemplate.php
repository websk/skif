<?php
namespace Skif;

class PhpTemplate
{
    const VIEWS_DIR_NAME = 'views';

    protected static function getRootSitePath()
    {
        return dirname(dirname(dirname(dirname(dirname(__DIR__)))));
    }

    public static function getSkifTemplatePath()
    {
        return dirname(dirname(__DIR__));
    }

    public static function getSkifPath()
    {
        return dirname(__DIR__);
    }

    public static function renderSkifTemplate($template_file, $variables = array()) {
        extract($variables, EXTR_SKIP);
        ob_start();

        require \Skif\PhpTemplate::getSkifTemplatePath() . DIRECTORY_SEPARATOR . $template_file;

        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    public static function renderTemplate($template_file, $variables = array()) {
        $relative_to_root_site_file_path =  \Skif\PhpTemplate::getRootSitePath() . DIRECTORY_SEPARATOR . $template_file;

        if (file_exists($relative_to_root_site_file_path)) {
            return \Skif\PhpTemplate::renderTemplateRelativeToRootSitePath($template_file, $variables);
        }

        return \Skif\PhpTemplate::renderSkifTemplate($template_file, $variables);
    }

    public static function renderTemplateRelativeToRootSitePath($template_file, $variables = array()) {
        extract($variables, EXTR_SKIP);
        ob_start();

        require \Skif\PhpTemplate::getRootSitePath() . DIRECTORY_SEPARATOR . $template_file;

        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    public static function renderTemplateBySkifModule($module, $template_file, $variables = array()) {
        if (\Skif\PhpTemplate::existsTemplateByModuleRelativeToRootSitePath($module, $template_file)) {
            $relative_to_root_site_file_path = self::VIEWS_DIR_NAME . DIRECTORY_SEPARATOR . 'Skif' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;
            return \Skif\PhpTemplate::renderTemplateRelativeToRootSitePath($relative_to_root_site_file_path, $variables);
        }

        extract($variables, EXTR_SKIP);
        ob_start();

        require \Skif\PhpTemplate::getSkifPath() . DIRECTORY_SEPARATOR . 'Skif' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . self::VIEWS_DIR_NAME . DIRECTORY_SEPARATOR . $template_file;
        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }

    public static function renderTemplateByModule($module, $template_file, $variables = array()) {
        $relative_to_root_site_file_path = self::VIEWS_DIR_NAME . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;

        if (file_exists(\Skif\PhpTemplate::getRootSitePath() . DIRECTORY_SEPARATOR . $relative_to_root_site_file_path)) {
            return \Skif\PhpTemplate::renderTemplateRelativeToRootSitePath($relative_to_root_site_file_path, $variables);
        }

        $relative_to_root_site_file_path = 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . self::VIEWS_DIR_NAME . DIRECTORY_SEPARATOR . $template_file;
        return \Skif\PhpTemplate::renderTemplateRelativeToRootSitePath($relative_to_root_site_file_path, $variables);
    }

    public static function existsTemplateByModuleRelativeToRootSitePath($module, $template_file)
    {
        $relative_to_root_site_file_path = \Skif\PhpTemplate::getRootSitePath() . DIRECTORY_SEPARATOR . self::VIEWS_DIR_NAME . DIRECTORY_SEPARATOR . 'Skif' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;

        return file_exists($relative_to_root_site_file_path);
    }

}
