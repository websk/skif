<?php

namespace Skif\Captcha;


use Skif\Path;

class Captcha
{
    /**
     * Проверка введенного кода
     * @return bool
     */
    public static function check()
    {
        if ((array_key_exists('captcha', $_REQUEST)) && ($_REQUEST['captcha'] == $_SESSION['captcha'])) {
            return true;
        }

        return false;
    }

    /**
     * Проверка для Jquery Form Validation
     * @return string
     */
    public static function checkAjax()
    {
        if (self::check()) {
            return 'true';
        }

        return 'false';
    }

    /**
     * Проверка с генерацией сообщения в случае ошибки
     * @return bool
     */
    public static function checkWithMessage()
    {
        if (self::check()) {
            return true;
        }

        \Skif\Messages::setError('Код, изображенный на картинке введен неверно. Попробуйте еще раз.');

        return false;
    }

    /**
     * Генерация картинки
     */
    public static function render()
    {
        $C_WIDTH = 140; // Ширина изображения
        $C_HEIGHT = 40; // Высота изображения
        $C_NUM_GENSIGN = 5; // Количество символов, которые нужно набрать
        $C_FONT_SIZE = 14;
        $path_fonts = Path::getSkifAssetsPath() . '/fonts/'; // Путь к шрифтам
        $numeric = 1; // Только цифры

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['captcha'] = '';

        $C_NUM_SIGN = intval(($C_WIDTH * $C_HEIGHT) / 150);

        $CODE = array();
        if (!$numeric) {
            $LETTERS = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '2', '3', '4', '5', '6', '7', '8', '9');
        } else {
            $LETTERS = array('1', '2', '3', '4', '5', '6', '7', '8', '9');
        }

        $FIGURES = array('50', '70', '90', '110', '130', '150', '170', '190', '210');

        //Создаем полотно
        $src = imagecreatetruecolor($C_WIDTH, $C_HEIGHT);

        //Заливаем фон
        $fon = imagecolorallocate($src, 255, 255, 255);
        imagefill($src, 0, 0, $fon);

        //Загрузка шрифтов
        $FONTS = [
            $path_fonts . 'font1.ttf',
            $path_fonts . 'font2.ttf'
        ];

        // Если есть шрифты
        if (sizeof($FONTS) > 0) {
            //Заливаем полотно буковками
            for ($i = 0; $i < $C_NUM_SIGN; $i++) {
                $h = 1;
                $color = imagecolorallocatealpha($src, rand(200, 200), rand(200, 200), rand(200, 200), 100);
                $font = $FONTS[rand(0, sizeof($FONTS) - 1)];
                $letter = mb_strtolower($LETTERS[rand(0, sizeof($LETTERS) - 1)]);
                $size = rand($C_FONT_SIZE - 1, $C_FONT_SIZE + 1);
                $angle = rand(0, 60);
                if ($h == rand(1, 2)) {
                    $angle = rand(360, 300);
                }
                //Пишем
                imagettftext($src, $size, $angle, rand($C_WIDTH * 0.1, $C_WIDTH - 20), rand($C_HEIGHT * 0.2, $C_HEIGHT - 10), $color, $font, $letter);
            }

            //Заливаем основными буковками
            for ($i = 0; $i < $C_NUM_GENSIGN; $i++) {
                // Ориентир
                $h = 1;

                $color = imagecolorallocatealpha($src, $FIGURES[rand(0, sizeof($FIGURES) - 1)], $FIGURES[rand(0, sizeof($FIGURES) - 1)], $FIGURES[rand(0, sizeof($FIGURES) - 1)], rand(10, 30));
                $font = $FONTS[rand(0, sizeof($FONTS) - 1)];
                $letter = mb_strtolower($LETTERS[rand(0, sizeof($LETTERS) - 1)]);
                $size = rand($C_FONT_SIZE * 2.1 - 1, $C_FONT_SIZE * 2.1 + 1);
                $x = (empty($x)) ? $C_WIDTH * 0.08 : $x + ($C_WIDTH * 0.8) / $C_NUM_GENSIGN + rand(0, $C_WIDTH * 0.01);
                $y = ($h == rand(1, 2)) ? (($C_HEIGHT * 1.15 * 3) / 4) + rand(0, $C_HEIGHT * 0.02) : (($C_HEIGHT * 1.15 * 3) / 4) - rand(0, $C_HEIGHT * 0.02);
                $angle = rand(5, 20);

                $CODE[] = $letter;
                if ($h == rand(1, 2)) {
                    $angle = rand(355, 340);
                }
                imagettftext($src, $size, $angle, $x, $y, $color, $font, $letter);
            }
        } else {
            //Заливаем точками
            for ($x = 0; $x < $C_WIDTH; $x++) {
                for ($i = 0; $i < ($C_HEIGHT * $C_WIDTH) / 1000; $i++) {
                    $color = imagecolorallocatealpha($src, $FIGURES[rand(0, sizeof($FIGURES) - 1)], $FIGURES[rand(0, sizeof($FIGURES) - 1)], $FIGURES[rand(0, sizeof($FIGURES) - 1)], rand(10, 30));
                    imagesetpixel($src, rand(0, $C_WIDTH), rand(0, $C_HEIGHT), $color);
                }
            }
            unset($x, $y);

            //Заливаем основными буковками
            for ($i = 0; $i < $C_NUM_GENSIGN; $i++) {
                $h = 1;
                $color = imagecolorallocatealpha($src, $FIGURES[rand(0, sizeof($FIGURES) - 1)], $FIGURES[rand(0, sizeof($FIGURES) - 1)], $FIGURES[rand(0, sizeof($FIGURES) - 1)], rand(10, 30));
                $letter = mb_strtolower($LETTERS[rand(0, sizeof($LETTERS) - 1)]);
                $x = (empty($x)) ? $C_WIDTH * 0.08 : $x + ($C_WIDTH * 0.8) / $C_NUM_GENSIGN + rand(0, $C_WIDTH * 0.01);
                $y = ($h == rand(1, 2)) ? (($C_HEIGHT * 1) / 4) + rand(0, $C_HEIGHT * 0.1) : (($C_HEIGHT * 1) / 4) - rand(0, $C_HEIGHT * 0.1);
                $CODE[] = $letter;
                imagestring($src, 5, $x, $y, $letter, $color);
            }
        }

        $_SESSION['captcha'] = mb_strtolower(implode('', $CODE));

        header("Content-type: image/png");
        imagepng($src);
        imagedestroy($src);
        exit;
    }

    public static function getUrl()
    {
        return '/captcha/generate';
    }
} 