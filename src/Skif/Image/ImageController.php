<?php

namespace Skif\Image;

class ImageController
{
    public static function uploadAction()
    {
        \Skif\Http::exit404If(!(count($_FILES) > 0));

        $file = $_FILES[0];

        $target_folder_in_images = '';

        if (array_key_exists('target_folder', $_POST)){
            $target_folder_in_images = $_POST['target_folder'];
        }

        echo self::processUpload($file, $target_folder_in_images);

        return;
    }

	public static function uploadToFilesAction()
	{
		\Skif\Http::exit404If(!(count($_FILES) > 0));

		$file = $_FILES[0];

        $root_images_folder = $site_path = \Skif\Conf\ConfWrapper::value('site_path') . '/images';

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
        \Skif\Http::exit404If(!(count($_FILES) > 0));

        $file = $_FILES[0];

        $root_images_folder = \Skif\Image\ImageConstants::IMG_ROOT_FOLDER;

        $target_folder_in_images = '';

        if (array_key_exists('target_folder', $_POST)){
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
     * Returns internal file name
     *
     * @param $file string
     * @return string
     */
    public static function processUpload($file, $target_folder_in_images, $root_images_folder = '')
    {
        $allowed_extensions = array("gif", "jpeg", "jpg", "png");
        $allowed_types = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png");

        $pathinfo = pathinfo($file["name"]);
        $file_extension = mb_strtolower($pathinfo['extension']);

        \Skif\Http::exit404If(!in_array($file["type"], $allowed_types));
        \Skif\Http::exit404If(!in_array($file_extension, $allowed_extensions));

        \Skif\Http::exit404If($file["error"] > 0);


        $image_manager = new \Skif\Image\ImageManager($root_images_folder);
        $internal_file_name = $image_manager->storeUploadedImageFile($file["name"], $file["tmp_name"], $target_folder_in_images);
        \Skif\Http::exit404If(!$internal_file_name);

        return $internal_file_name;
    }
} 