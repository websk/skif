<?php

namespace Skif;


class Image
{

    public static function uploadAndResizeImage($destination_file_path, $resize_value, $prefix, $index, $quality, $to_width = true)
    {
        $tmp_dir = \WebSK\Skif\ConfWrapper::value('tmp_path');

        if (!is_dir($tmp_dir)) {
            $tmp_dir = '';
        }

        $destination_file_path = rtrim($destination_file_path, '/');

        if (!is_dir($destination_file_path)) {
            if (!mkdir($destination_file_path, 0777, true)) {
                throw new \Exception('Не удалось создать директорию: ' . $destination_file_path);
            }
        }

        $images_arr = $_FILES['image_file'];
        $images_names_arr = $images_arr['name'];
        $images_tmp_path_arr = $images_arr['tmp_name'];

        $uploaded_files = array();

        for ($i = 0; $i < count($images_names_arr); $i++) {
            if (empty($images_names_arr[$i])) {
                continue;
            }

            if (empty($images_tmp_path_arr[$i])) {
                continue;
            }

            $file_info = new \SplFileInfo($images_names_arr[$i]);
            $extension = $file_info->getExtension();
            $extension = strtolower($extension);

            if (!in_array($extension, array('jpg', 'gif', 'jpeg', 'png'))) {
                continue;
            }

            $images_path_arr[$i] = $tmp_dir . '/' . $images_names_arr[$i];

            move_uploaded_file($images_tmp_path_arr[$i], $images_path_arr[$i]);

            $uploaded_files[$i] = $prefix;
            if ($index) {
                $uploaded_files[$i] .= '-' . ($index + $i);
            }
            $uploaded_files[$i] .= '.' . $extension;

            $image_path = $destination_file_path . '/' . $uploaded_files[$i];

            if (!file_exists($images_path_arr[$i])) {
                continue;
            }

            $HW = getimagesize($images_path_arr[$i]);

            if ($to_width) {
                if ($HW[0] < $resize_value) {
                    $resize_value = $HW[0];
                }
            } else {
                if ($HW[1] < $resize_value) {
                    $resize_value = $HW[1];
                }
            }

            self::resizeImage($images_path_arr[$i], $image_path, $resize_value, $quality, 0);

            unlink($images_path_arr[$i]);
        }

        return $uploaded_files;
    }

    /**
     * @param $src - имя исходного файла
     * @param $dest - имя генерируемого файла
     * @param $resize_value
     * @param $quality - качество генерируемого изображения
     * @param bool $to_width -  обрезать по ширине или высоте
     * @param int $copy
     * @return bool
     */
    public static function resizeImage($src, $dest, $resize_value, $quality, $to_width = true, $copy = 0)
    {
        if (!file_exists($src)) {
            return false;
        }

        $size = getimagesize($src);
        if ($size === false) {
            return false;
        }

        $format = strtolower(substr($size['mime'], strpos($size['mime'], '/') + 1));
        $icfunc = "imagecreatefrom" . $format;

        if ($format == 'png') {
            $quality = ceil($quality/10);
        }

        if (!function_exists($icfunc)) {
            return false;
        }

        if ($to_width) {
            $width = $resize_value;
            $ratio = $size[0] / $width;
            $height = round($size[1] / $ratio);
        } else {
            $height = $resize_value;
            $ratio = $size[1] / $height;
            $width = round($size[0] / $ratio);
        }

        $rgb = 0xFFFFFF;

        $isrc = $icfunc($src);
        $idest = imagecreatetruecolor($width, $height);
        // $rgb - цвет фона, по умолчанию - белый
        imagefill($idest, 0, 0, $rgb);

        imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);

        /*
        if ($copy==1){
            $white = ImageColorAllocate($idest, 255, 255, 255);
            ImageString($idest, 5, 10, $height-22, $site_url, $white);
        }
        */

        $formatfunc = 'image' . $format;
        $formatfunc($idest, $dest, $quality);

        imagedestroy($isrc);
        imagedestroy($idest);

        return true;
    }


} 