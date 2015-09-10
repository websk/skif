<?php
/**
 * Created by PhpStorm.
 * User: Кульков
 * Date: 26.12.13
 * Time: 15:51
 */

namespace Skif;


class FileUtils {

    public static function renderFileContent($file_path)
    {
        $file_info = new \SplFileInfo($file_path);

        if (!$file_info->isFile()) {
            return;
        }

        $download_size = $file_info->getSize();

        $file_name = str_replace(' ', '_', urldecode($file_info->getFilename()));

        header('Content-type: application/' . $file_info->getExtension());
        header("Content-Disposition: attachment; filename=" . $file_name . ";");
        header("Accept-Ranges: bytes");
        header("Content-Length: " . $download_size);

        readfile($file_path);
    }

    /**
     * Загрузка файла на сервер
     * @param $path
     * @param string $file_name
     * @return bool|string
     */
    public static function uploadFile($path, $file_name = '')
    {
        $conf = \Skif\Conf::get();

        $file = array('files', $_FILES) ? $_FILES['file'] : null;

        if (!$file) {
            return false;
        }

        $file_tmp_path = $conf['tmp_path'] . '/' . $file['name'];
        move_uploaded_file($file['tmp_name'], $file_tmp_path);

        $file_info = new \SplFileInfo($file_tmp_path);

        if (!$file_info->isFile()) {
            return false;
        }

        if ($file_name) {
            $file_name .= '.' . $file_info->getExtension();
        } else {
            $file_name = $file['name'];
        }

        if (file_exists($conf['site_path'] . $path . '/' . $file_name)) {
            unlink($conf['site_path'] . $path . '/' . $file_name);
        }

        copy($file_tmp_path, $conf['site_path'] . $path . '/' . $file_name);

        unlink($file_tmp_path);

        return $file_name;
    }

    /**
     * Удаление файла
     * @param $file_path
     * @return bool
     */
    public static function deleteFile($file_path)
    {
        $conf = \Skif\Conf::get();

        if (!file_exists($conf['site_path'] . $file_path)) {
            return false;
        }

        unlink($conf['site_path'] . $file_path);

        return true;

    }

    /**
     * Удаление каталога с подкаталогами и файлами
     * @param $directory
     */
    public static function deleteDir($directory)
    {
        if (is_dir($directory)) {
            $dirs = @opendir($directory);
            while (($filedirs = readdir($dirs)) !== false)
                if ($filedirs != "." and $filedirs != "..") {
                    if (is_dir($directory .'/' . $filedirs)) {
                        \Skif\FileUtils::deleteDir($directory .'/' . $filedirs);
                    }
                    else {
                        unlink($directory .'/' . $filedirs);
                    }
                }
            closedir($dirs);

            rmdir($directory);
        }
    }

    /**
     * Проверка имени файла
     * @param $file_name
     * @return bool
     */
    public static function checkFileName($file_name)
    {
        if (preg_match("/[^a-z0-9_-]/i", $file_name)) {
            return false;
        }

        return true;
    }

}