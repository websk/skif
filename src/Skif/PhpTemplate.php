<?php
namespace Skif;

class PhpTemplate
{
    public static function renderSkifTemplate($template_file, $variables = array()) {
        extract($variables, EXTR_SKIP);
        ob_start();

        require \Skif\Path::getSkifViewsPath() . DIRECTORY_SEPARATOR . $template_file;

        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    public static function renderTemplate($template_file, $variables = array()) {
        $relative_to_root_site_file_path = \Skif\Path::getSiteViewsPath() . DIRECTORY_SEPARATOR . $template_file;

        if (file_exists($relative_to_root_site_file_path)) {
            return \Skif\PhpTemplate::renderTemplateRelativeToRootSitePath($template_file, $variables);
        }

        return \Skif\PhpTemplate::renderTemplate($template_file, $variables);
    }

    public static function renderTemplateRelativeToRootSitePath($template_file, $variables = array()) {
        extract($variables, EXTR_SKIP);
        ob_start();

        require \Skif\Path::getSiteViewsPath() . DIRECTORY_SEPARATOR . $template_file;

        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    public static function renderTemplateBySkifModule($module, $template_file, $variables = array()) {
        if (\Skif\PhpTemplate::existsTemplateByModuleRelativeToRootSitePath($module, $template_file)) {
            $relative_to_root_site_file_path = 'Skif' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;
            return \Skif\PhpTemplate::renderTemplateRelativeToRootSitePath($relative_to_root_site_file_path, $variables);
        }

        extract($variables, EXTR_SKIP);
        ob_start();

        require \Skif\Path::getSkifAppPath() . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . \Skif\Path::VIEWS_DIR_NAME . DIRECTORY_SEPARATOR . $template_file;
        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }

    public static function renderTemplateByModule($module, $template_file, $variables = array()) {
        $relative_to_root_site_file_path = \Skif\Path::VIEWS_DIR_NAME . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;

        if (file_exists(\Skif\Path::getRootSitePath() . DIRECTORY_SEPARATOR . $relative_to_root_site_file_path)) {
            return \Skif\PhpTemplate::renderTemplateRelativeToRootSitePath($relative_to_root_site_file_path, $variables);
        }

        $relative_to_root_site_file_path = 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . \Skif\Path::VIEWS_DIR_NAME . DIRECTORY_SEPARATOR . $template_file;
        return \Skif\PhpTemplate::renderTemplateRelativeToRootSitePath($relative_to_root_site_file_path, $variables);
    }

    public static function existsTemplateByModuleRelativeToRootSitePath($module, $template_file)
    {
        $relative_to_root_site_file_path = \Skif\Path::getSiteViewsPath() . DIRECTORY_SEPARATOR . 'Skif' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;

        return file_exists($relative_to_root_site_file_path);
    }

}
