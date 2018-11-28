<?php

namespace WebSK\Utils;

/**
 * Class Filters
 * @package WebSK\Utils
 */
class Filters
{
    /**
     * Проверка Email
     * @param string $email
     * @return bool
     */
    public static function checkEmail(string $email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $text
     * @return string
     */
    public static function checkPlain(string $text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
    }
}
