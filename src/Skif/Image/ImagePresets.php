<?php

namespace Skif\Image;

class ImagePresets
{

    const IMAGE_PRESET_604_331  = '604_331';
    const IMAGE_PRESET_510_390  = '510_390';
    const IMAGE_PRESET_800_600  = '800_600';
    const IMAGE_PRESET_160_200  = '160_200';
    const IMAGE_PRESET_800_800_auto = '800_800_auto';
    const IMAGE_PRESET_600_auto = '600_auto';
    const IMAGE_PRESET_400_auto = '400_auto';
    const IMAGE_PRESET_120_auto = '120_auto';
    const IMAGE_PRESET_160_auto = '160_auto';
    const IMAGE_PRESET_200_auto = '200_auto';
    const IMAGE_PRESET_30_30 = '30_30';

    const IMAGE_PRESET_UPLOAD = 'upload';

    public static function processImageByPreset(\Imagine\Image\ImageInterface $imageObject, $presetName)
    {

        switch ($presetName) {
            case self::IMAGE_PRESET_604_331:

                $imageSize = $imageObject->getSize();
                $thumbnail = $imageObject->copy();
                $size = new \Imagine\Image\Box(604, 331);

                $ratios = array(
                    $size->getWidth() / $imageSize->getWidth(),
                    $size->getHeight() / $imageSize->getHeight()
                );
                $ratio = max($ratios);

                $imageSize = $thumbnail->getSize()->scale($ratio);
                $thumbnail->resize($imageSize);

                $result = $thumbnail->crop(new \Imagine\Image\Point(
                    max(0, round(($imageSize->getWidth() - $size->getWidth()) / 2)),
                    max(0, round(($imageSize->getHeight() - $size->getHeight()) / 2))
                ), $size);

                return $result;
                break;
            case self::IMAGE_PRESET_510_390:
                $imageSize = $imageObject->getSize();
                $thumbnail = $imageObject->copy();
                $size = new \Imagine\Image\Box(510, 390);

                $ratios = array(
                    $size->getWidth() / $imageSize->getWidth(),
                    $size->getHeight() / $imageSize->getHeight()
                );
                $ratio = max($ratios);

                $imageSize = $thumbnail->getSize()->scale($ratio);
                $thumbnail->resize($imageSize);

                $result = $thumbnail->crop(new \Imagine\Image\Point(
                    max(0, round(($imageSize->getWidth() - $size->getWidth()) / 2)),
                    max(0, round(($imageSize->getHeight() - $size->getHeight()) / 2))
                ), $size);

                return $result;
                break;

            case self::IMAGE_PRESET_30_30:
                return $imageObject->thumbnail(new \Imagine\Image\Box(30, 30), \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND);
                break;
            case self::IMAGE_PRESET_800_600:
                return $imageObject->thumbnail(new \Imagine\Image\Box(800, 600), \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND);
                break;
            case self::IMAGE_PRESET_160_200:
                return $imageObject->thumbnail(new \Imagine\Image\Box(160, 200), \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND);
                break;
            case '40_40':
                return $imageObject->thumbnail(new \Imagine\Image\Box(40, 40), \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND);
                break;
	        case self::IMAGE_PRESET_800_800_auto:

		        return $imageObject->thumbnail(new \Imagine\Image\Box(800, 800), \Imagine\Image\ImageInterface::THUMBNAIL_INSET);
		        break;
            case self::IMAGE_PRESET_UPLOAD:
                //$box = $imageObject->getSize();
                //return $imageObject->resize($box->widen(2000));
                return $imageObject->thumbnail(new \Imagine\Image\Box(2000, 2000));
                break;

            case self::IMAGE_PRESET_600_auto:
                return $imageObject->thumbnail(new \Imagine\Image\Box(600, 2000), \Imagine\Image\ImageInterface::THUMBNAIL_INSET);
                break;

            case self::IMAGE_PRESET_400_auto:
                return $imageObject->thumbnail(new \Imagine\Image\Box(400, 2000), \Imagine\Image\ImageInterface::THUMBNAIL_INSET);
                break;

            case self::IMAGE_PRESET_120_auto:
                return $imageObject->thumbnail(new \Imagine\Image\Box(120, 2000), \Imagine\Image\ImageInterface::THUMBNAIL_INSET);
                break;

            case self::IMAGE_PRESET_160_auto:
                return $imageObject->thumbnail(new \Imagine\Image\Box(160, 2000), \Imagine\Image\ImageInterface::THUMBNAIL_INSET);
                break;

            case self::IMAGE_PRESET_200_auto:
                return $imageObject->thumbnail(new \Imagine\Image\Box(200, 2000), \Imagine\Image\ImageInterface::THUMBNAIL_INSET);
                break;

            default:
                \Skif\Http::exit404();
                error_log('Preset "' . $presetName . '" is not set');
                break;
        }

    }

}
