<?php

namespace Websk\Utils;

use WebSK\Skif\ConfWrapper;

/**
 * Class FileUtils
 * @package Skif
 */
class FileUtils
{
    /**
     * @param string $file_path
     */
    public static function renderFileContent(string $file_path)
    {
        $file_info = new \SplFileInfo($file_path);

        if (!$file_info->isFile()) {
            return;
        }

        $download_size = $file_info->getSize();

        $file_name = str_replace(' ', '_', urldecode($file_info->getFilename()));

        header(HTTP::HEADER_CONTENT_TYPE . ': application/' . $file_info->getExtension());
        header(HTTP::HEADER_CONTENT_DISPOSITION . ": attachment; filename=" . $file_name . ";");
        header(HTTP::HEADER_ACCEPT_RANGES . ": bytes");
        header(HTTP::HEADER_CONTENT_LENGTH . ": " . $download_size);

        readfile($file_path);
    }

    /**
     * Загрузка файла на сервер
     * @param string $path
     * @param string $file_name
     * @return bool|string
     */
    public static function uploadFile(string $path, string $file_name = '')
    {
        $file = array_key_exists('file', $_FILES) ? $_FILES['file'] : null;

        if (!$file) {
            return false;
        }

        $file_tmp_path = ConfWrapper::value('tmp_path') . DIRECTORY_SEPARATOR . $file['name'];
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

        $site_path = ConfWrapper::value('site_path');

        if (file_exists($site_path . $path . DIRECTORY_SEPARATOR . $file_name)) {
            unlink($site_path . $path . DIRECTORY_SEPARATOR . $file_name);
        }

        copy($file_tmp_path, $site_path . $path . DIRECTORY_SEPARATOR . $file_name);

        unlink($file_tmp_path);

        return $file_name;
    }

    /**
     * Удаление файла
     * @param string $file_path
     * @return bool
     */
    public static function deleteFile(string $file_path)
    {
        $site_path = ConfWrapper::value('site_path');

        if (!file_exists($site_path . $file_path)) {
            return false;
        }

        unlink($site_path . $file_path);

        return true;
    }

    /**
     * Удаление каталога с подкаталогами и файлами
     * @param string $directory
     */
    public static function deleteDir(string $directory)
    {
        if (!is_dir($directory)) {
            return;
        }

        $dirs = @opendir($directory);
        while (($filedirs = readdir($dirs)) !== false) {
            if ($filedirs != "." and $filedirs != "..") {
                if (is_dir($directory . DIRECTORY_SEPARATOR . $filedirs)) {
                    self::deleteDir($directory . DIRECTORY_SEPARATOR . $filedirs);
                } else {
                    unlink($directory . DIRECTORY_SEPARATOR . $filedirs);
                }
            }
        }

        closedir($dirs);
        rmdir($directory);
    }

    /**
     * Проверка имени файла
     * @param string $file_name
     * @return bool
     */
    public static function checkFileName(string $file_name)
    {
        if (preg_match("/[^a-z0-9_-]/i", $file_name)) {
            return false;
        }

        return true;
    }
}
