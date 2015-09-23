<?php
namespace Skif;

class PhpTemplate
{
    /**
     * Вывод шаблона относительно views
     * @param $template_file
     * @param array $variables
     * @return string
     */
    public static function renderTemplate($template_file, $variables = array()) {
        $relative_to_site_views_file_path = \Skif\Path::getSiteViewsPath() . DIRECTORY_SEPARATOR . $template_file;

        if (file_exists($relative_to_site_views_file_path)) {
            return \Skif\PhpTemplate::renderTemplateRelativeToRootSitePath($template_file, $variables);
        }


        $relative_to_skif_views_file_path = \Skif\Path::getSkifViewsPath() . DIRECTORY_SEPARATOR . $template_file;

        if (file_exists($relative_to_skif_views_file_path)) {
            extract($variables, EXTR_SKIP);
            ob_start();

            require $relative_to_skif_views_file_path;

            $contents = ob_get_contents();
            ob_end_clean();

            return $contents;
        }

        return  '';
    }

    protected static function renderTemplateRelativeToRootSitePath($template_file, $variables = array()) {
        extract($variables, EXTR_SKIP);
        ob_start();

        require \Skif\Path::getSiteViewsPath() . DIRECTORY_SEPARATOR . $template_file;

        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    public static function renderTemplateBySkifModule($module, $template_file, $variables = array()) {
        if (\Skif\PhpTemplate::existsTemplateBySkifModuleRelativeToRootSitePath($module, $template_file)) {
            $relative_to_root_site_file_path = 'modules' . DIRECTORY_SEPARATOR . 'Skif' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;

            return \Skif\PhpTemplate::renderTemplateRelativeToRootSitePath($relative_to_root_site_file_path, $variables);
        }

        extract($variables, EXTR_SKIP);
        ob_start();

        require \Skif\Path::getSkifAppPath() . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . \Skif\Path::VIEWS_DIR_NAME . DIRECTORY_SEPARATOR . $template_file;
        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }

    public static function existsTemplateBySkifModuleRelativeToRootSitePath($module, $template_file)
    {
        $relative_to_root_site_file_path = \Skif\Path::getSiteViewsPath() . DIRECTORY_SEPARATOR  . 'modules' . DIRECTORY_SEPARATOR . 'Skif' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;

        return file_exists($relative_to_root_site_file_path);
    }

    public static function renderTemplateByModule($module, $template_file, $variables = array()) {
        if (file_exists(\Skif\Path::getSiteViewsPath() . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file)) {
            return \Skif\PhpTemplate::renderTemplateRelativeToRootSitePath('modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file, $variables);
        }


        $relative_to_root_site_file_path = 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . \Skif\Path::VIEWS_DIR_NAME . DIRECTORY_SEPARATOR . $template_file;

        extract($variables, EXTR_SKIP);
        ob_start();

        require \Skif\Path::getRootSitePath() . DIRECTORY_SEPARATOR . $relative_to_root_site_file_path;
        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }

}
