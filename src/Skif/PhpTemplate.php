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
     * @param array $variables
     * @return string
     */
    public static function renderTemplateBySkifModule($module, $template_file, $variables = array())
    {
        if (self::existsTemplateBySkifModuleRelativeToRootSitePath($module, $template_file)) {
            $relative_to_root_site_file_path = 'modules' . DIRECTORY_SEPARATOR . 'Skif' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;

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
     * @return bool
     */
    public static function existsTemplateBySkifModuleRelativeToRootSitePath($module, $template_file)
    {
        $relative_to_root_site_file_path = Path::getSiteViewsPath() . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Skif' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $template_file;

        return file_exists($relative_to_root_site_file_path);
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
