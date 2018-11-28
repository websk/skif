<?php

namespace WebSK\Skif\Image;

use WebSK\Slim\ConfWrapper;
use WebSK\Utils\Exits;

/**
 * Class ImageController
 * @package WebSK\WebSK\Skif\Image\Image
 */
class ImageController
{
    /*
    public static function uploadAction()
    {
        Exits::exit404If(!(count($_FILES) > 0));

        $file = $_FILES[0];

        $target_folder_in_images = '';

        if (array_key_exists('target_folder', $_POST)){
            $target_folder_in_images = $_POST['target_folder'];
        }

        echo self::processUpload($file, $target_folder_in_images);

        return;
    }
    */

    public function uploadAction()
    {
        // TODO: Проверка прав доступа

        echo self::processUploadImage();
    }

    public static function processUploadImage()
    {
        $root_images_folder = ImageConstants::IMG_ROOT_FOLDER;

        $json_arr = array();

        if (array_key_exists('name', $_FILES['upload_image']) && is_array($_FILES['upload_image']['name'])) {
            $files_arr = \WebSK\Skif\Utils::rebuildFilesArray($_FILES['upload_image']);
        } else {
            $files_arr[] = $_FILES['upload_image'];
        }

        $target_folder = '';
        if (array_key_exists('target_folder', $_POST)) {
            $target_folder = $_POST['target_folder'];
        }

        $file_name = \WebSK\Skif\Image\ImageController::processUpload($files_arr[0], $target_folder, $root_images_folder);
        if (!$file_name) {
            $json_arr['status'] = 'error';
        }

        $image_path = $target_folder . DIRECTORY_SEPARATOR . $file_name;

        $json_arr['files'][] = array(
            'name' => $file_name,
            'size' => 902604,
            'url' => ImageManager::getImgUrlByFileName($image_path),
            'thumbnailUrl' => ImageManager::getImgUrlByPreset($image_path, '160_auto'),
            'deleteUrl' => "",
            'deleteType' => "DELETE"
        );

        $json_arr['status'] = 'success';

        return json_encode($json_arr);
    }

    public static function uploadToFilesAction()
    {
        Exits::exit404If(!(count($_FILES) > 0));

        $file = $_FILES[0];

        $root_images_folder = $site_path = ConfWrapper::value('site_path') . '/images';

        $file_name = self::processUpload($file, '', $root_images_folder);

        $response = array(
            'fileName' => $file_name,
            'filePath' => $root_images_folder,
        );

        echo json_encode($response);

        return;
    }

    public static function uploadToImagesAction()
    {
        Exits::exit404If(!(count($_FILES) > 0));

        $file = $_FILES[0];

        $root_images_folder = ImageConstants::IMG_ROOT_FOLDER;

        $target_folder_in_images = '';

        if (array_key_exists('target_folder', $_POST)) {
            $target_folder_in_images = $_POST['target_folder'];
        }

        $file_name = self::processUpload($file, $target_folder_in_images, $root_images_folder);

        $response = array(
            'fileName' => $file_name,
            'filePath' => $root_images_folder,
        );

        header('Content-Type: application/json');

        echo json_encode($response);

        return;
    }

    /**
     * @param $file
     * @param string $target_folder_in_images
     * @param string $root_images_folder
     * @return string
     */
    public static function processUpload($file, string $target_folder_in_images, string $root_images_folder = '')
    {
        $allowed_extensions = array("gif", "jpeg", "jpg", "png");
        $allowed_types = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png");

        $pathinfo = pathinfo($file["name"]);
        $file_extension = mb_strtolower($pathinfo['extension']);

        Exits::exit404If(!in_array($file["type"], $allowed_types));
        Exits::exit404If(!in_array($file_extension, $allowed_extensions));

        Exits::exit404If($file["error"] > 0);


        $image_manager = new ImageManager($root_images_folder);
        $internal_file_name = $image_manager->storeUploadedImageFile($file["name"], $file["tmp_name"],
            $target_folder_in_images);
        Exits::exit404If(!$internal_file_name);

        return $internal_file_name;
    }
}
