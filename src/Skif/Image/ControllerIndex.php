<?php

namespace Skif\Image;

class ControllerIndex
{

    public function indexAction($presetName, $imageName)
    {
        $image = new \Skif\Image\ImageManager();

        $baseUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $image->output($baseUrl);//it outputs image and call exit
    }
}
