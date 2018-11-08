<?php

namespace Skif\Widgets\ImageUploadWidget;

class ImageUploadWidget {
    public static function renderWidget($field_name, $field_value, $widget_options = array())
    {
        $widget_options['field_name'] = $field_name;
        $widget_options['field_value'] = $field_value;

        $output = \Skif\PhpTemplate::renderTemplateBySkifModule(
            'Widgets' . DIRECTORY_SEPARATOR . 'ImageUploadWidget',
            'image_upload_widget.tpl.php',
            $widget_options
        );

        return $output;
    }

    public static function getIconImagePresetFolder()
    {
        return \WebSK\Skif\Image\ImageManager::getPresetUrlByName(\WebSK\Skif\Image\ImagePresets::IMAGE_PRESET_200_auto);
    }

    public static function getIconFileUrlByFilename($file_name)
    {
        //return self::getIconFolder().DIRECTORY_SEPARATOR.$file_name;
        return \WebSK\Skif\Image\ImageManager::getImgUrlByPreset($file_name, \WebSK\Skif\Image\ImagePresets::IMAGE_PRESET_200_auto);
    }

}