<?php

namespace Skif\CRUD\ImageUploadToFilesWidget;


class ImageUploadToFilesWidget {

	const IMAGE_ICON_PRESET = 'fotobank263x263Auto';

	public static function renderWidget($field_name, $field_value)
	{
		$output = \Skif\PhpTemplate::renderTemplate('templates/image_upload_to_files_widget.tpl.php', array(
			'field_name' => $field_name,
			'field_value' => $field_value
		));

		return $output;
	}

	public static function getIconFolder()
	{
		return \Skif\Imagecache::imagecache_create_url(self::IMAGE_ICON_PRESET);
	}

	public static function getIconFile($file_name)
	{
		return self::getIconFolder().DIRECTORY_SEPARATOR.$file_name;
	}
} 