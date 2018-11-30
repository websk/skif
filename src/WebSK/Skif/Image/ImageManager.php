<?php

namespace WebSK\Skif\Image;

use Imagine\Gd\Imagine;
use WebSK\Utils\Exits;

/**
 * Class ImageManager
 * @package WebSK\Skif\Image\Image
 */
class ImageManager
{

    /**
     * Imagine library Imagick adapter
     * @var Imagine
     */
    protected $imagine;

    protected $error;

    protected $root_folder;

    public function __construct($root_folder = '')
    {
        $this->imagine = new Imagine();
        if (empty($root_folder)) {
            $this->root_folder = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . ImageConstants::IMG_ROOT_FOLDER;
        } else {
            $this->root_folder = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $root_folder;
        }
    }

    public function removeImageFile($fileName)
    {
        $imgPath = $this->getImagesRootFolder() . DIRECTORY_SEPARATOR . $fileName;
        return unlink($imgPath);
    }

    public function storeUploadedImageFile($fileName, $tmpFileName, $target_folder_in_images)
    {
        if (!\is_uploaded_file($tmpFileName)) {
            return '';
        }

        return $this->storeImageFile($fileName, $tmpFileName, $target_folder_in_images);
    }

    public function storeImageFile($fileName, $tmpFileName, $target_folder_in_images)
    {
        $image_path_in_images_components_arr = [];
        if ($target_folder_in_images != '') {
            $image_path_in_images_components_arr[] = $target_folder_in_images;
        }

        $unique_filename = $this->getUniqueImageName($fileName);
        $image_path_in_images_components_arr[] = $unique_filename;

        $newName = implode(DIRECTORY_SEPARATOR, $image_path_in_images_components_arr);

        $newPath = $this->getImagesRootFolder() . DIRECTORY_SEPARATOR . $newName;

        $destination_file_path = pathinfo($newPath, PATHINFO_DIRNAME);
        if (!is_dir($destination_file_path)) {
            if (!mkdir($destination_file_path, 0777, true)) {
                throw new \Exception('Не удалось создать директорию: ' . $destination_file_path);
            }
        }

        $file_extension = pathinfo($newName, PATHINFO_EXTENSION);

        $tmp_dir = $this->root_folder . DIRECTORY_SEPARATOR . 'tmp';
        if (!is_dir($tmp_dir)) {
            if (!mkdir($tmp_dir, 0777, true)) {
                throw new \Exception('Не удалось создать директорию: ' . $tmp_dir);
            }
        }

        // уникальное случайное имя файла
        do {
            $tmp_dest_file = $tmp_dir . DIRECTORY_SEPARATOR . 'imagemanager_' . mt_rand(0, 1000000) . '.' . $file_extension;
        } while (file_exists($tmp_dest_file));

        //try {
        $image = $this->imagine->open($tmpFileName);
        $image = ImagePresets::processImageByPreset($image, ImageConstants::DEFAULT_UPLOAD_PRESET);

        // запись во временный файл, чтобы другой процесс не мог получить доступ к недописанному файлу
        $image->save($tmp_dest_file, array());

        // переименовываем временный файл
        if (!rename($tmp_dest_file, $newPath)) {
            throw new \Exception('Не удалось переместить файл: ' . $tmp_dest_file . ' -> ' . $newPath);
        }

        return $unique_filename;
    }

    public function storeRemoteImageFile($file_url, $target_folder_in_images = '')
    {
        $new_name = $this->getUniqueImageName('temp.jpg');

        $new_path = $this->getImagesRootFolder() . DIRECTORY_SEPARATOR . $new_name;
        if ($target_folder_in_images != '') {
            $new_path = $this->getImagesRootFolder() . DIRECTORY_SEPARATOR . $target_folder_in_images . DIRECTORY_SEPARATOR . $new_name;
        }

        $image = $this->imagine->open($file_url);
        $image = ImagePresets::processImageByPreset($image, ImageConstants::DEFAULT_UPLOAD_PRESET);
        $image->save($new_path, array());

        return $new_name;
    }

    public function output($fileUrl)
    {
        list($imageName, $presetName) = $this->acquirePresetNameAndImageNameFromUrl($fileUrl);
        $fullpath = $this->getImagePathByPreset($imageName, $presetName);

        if (!file_exists($fullpath)) {
            //$this->genImageByPreset($imageName, $presetName);

            $imgPath = $this->getImagesRootFolder() . DIRECTORY_SEPARATOR . $imageName;

            if (!file_exists($imgPath)) {
                Exits::exit404();
            }

            $res = $this->moveImageByPreset($imgPath, $fullpath, $presetName);
        }
        $ext = pathinfo($fullpath, PATHINFO_EXTENSION);

        $fp = fopen($fullpath, 'rb');
        header("Content-Type: image/" . $ext);
        header("Content-Length: " . filesize($fullpath));
        fpassthru($fp);
        exit;
    }

    public function moveImageByPreset($imagePath, $presetPath, $presetName)
    {
        //try {
        $image = $this->imagine->open($imagePath);

        $presetDir = dirname($presetPath);

        if (!\file_exists($presetDir)) {
            $res = mkdir($presetDir, 0777, true);
            if (!$res) {
                $this->error = "Unable to create path: " . $presetDir;
                return false;
            }
        }

        $file_extension = pathinfo($presetPath, PATHINFO_EXTENSION);

        $tmp_dir = $this->root_folder . DIRECTORY_SEPARATOR . 'tmp';
        if (!is_dir($tmp_dir)) {
            if (!mkdir($tmp_dir, 0777, true)) {
                throw new \Exception('Не удалось создать директорию: ' . $tmp_dir);
            }
        }

        // уникальное случайное имя файла
        do {
            $tmp_dest_file = $tmp_dir . DIRECTORY_SEPARATOR . 'imagemanager_' . mt_rand(0, 1000000) . '.' . $file_extension;
        } while (file_exists($tmp_dest_file));

        $image = ImagePresets::processImageByPreset($image, $presetName);

        // запись во временный файл, чтобы другой процесс не мог получить доступ к недописанному файлу
        $image->save($tmp_dest_file, array('quality' => 100));

        // переименовываем временный файл
        if (!rename($tmp_dest_file, $presetPath)) {
            throw new \Exception('Не удалось переместить файл: ' . $tmp_dest_file . ' -> ' . $presetPath);
        }

        //} catch (\Imagine\Exception\Exception $e) {
        //  return '';
        //}

        return true;
    }

    public function getUniqueImageName($userImageName)
    {
        $ext = pathinfo($userImageName, PATHINFO_EXTENSION);
        $imageName = str_replace(".", "", uniqid(md5($userImageName), true)) . "." . $ext;

        return $imageName;
    }

    public function getImagePathByPreset($imageName, $presetName)
    {
        $imagesPathInFilesystem = $this->getImagesRootFolder();
        return
            $imagesPathInFilesystem
            . DIRECTORY_SEPARATOR
            . ImageConstants::IMG_PRESETS_FOLDER
            . DIRECTORY_SEPARATOR
            . $presetName
            . DIRECTORY_SEPARATOR
            . $imageName;
    }

    public function acquirePresetNameAndImageNameFromUrl($requested_file_path)
    {
        $requested_file_path = ltrim($requested_file_path, '/');

        $file_path_parts_arr = explode(ImageConstants::IMG_PRESETS_FOLDER . '/', $requested_file_path);
        $image_path_parts_arr = explode('/', $file_path_parts_arr[1]);
        $preset_name = array_shift($image_path_parts_arr);
        $file_path_relative = implode('/', $image_path_parts_arr);

        return array($file_path_relative, $preset_name);
    }

    public function getImagesRootFolder()
    {
        return $this->root_folder;
    }

    public static function getImgUrlByPreset($imageName, $presetName)
    {
        $preset_url = self::getPresetUrlByName($presetName);
        $image_url = $preset_url . DIRECTORY_SEPARATOR . $imageName;

        return $image_url;
    }

    public static function getImgUrlByFileName($imageName)
    {
        return DIRECTORY_SEPARATOR . ImageConstants::IMG_ROOT_FOLDER . DIRECTORY_SEPARATOR . $imageName;
    }

    public static function getPresetUrlByName($preset_name)
    {
        return DIRECTORY_SEPARATOR . ImageConstants::IMG_ROOT_FOLDER . DIRECTORY_SEPARATOR . ImageConstants::IMG_PRESETS_FOLDER . DIRECTORY_SEPARATOR . $preset_name;
    }
}
