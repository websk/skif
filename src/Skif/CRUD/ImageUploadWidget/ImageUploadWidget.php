<?php

namespace Skif\CRUD\ImageUploadWidget;

class ImageUploadWidget {
    public static function renderWidget($field_name, $field_value, $widget_options = array())
    {
        $widget_options['field_name'] = $field_name;
        $widget_options['field_value'] = $field_value;

        $output = \Skif\PhpTemplate::renderTemplate('templates/image_upload_widget.tpl.php', $widget_options);

        return $output;
    }

    public static function getIconImagePresetFolder()
    {
        return \Skif\Image\ImageManager::getPresetUrlByName(\Skif\Image\ImagePresets::IMAGE_PRESET_FOTOBANK_263_263_auto);
    }

    public static function getIconFileUrlByFilename($file_name)
    {
        //return self::getIconFolder().DIRECTORY_SEPARATOR.$file_name;
        return \Skif\Image\ImageManager::getImgUrlByPreset($file_name, \Skif\Image\ImagePresets::IMAGE_PRESET_FOTOBANK_263_263_auto);
    }

}