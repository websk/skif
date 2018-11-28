<?php

namespace Websk\Skif;

/**
 * Class Messages
 * @package WebSK\Skif
 */
class Messages
{
    /**
     * @param string $key
     * @param string $value
     */
    protected static function setMessageValue(string $key, string $value)
    {
        $_SESSION['messages'][$key] = $value;
    }

    /**
     * @param string $message
     */
    public static function setError(string $message)
    {
        self::setMessageValue('danger', $message);
    }

    /**
     * @param string $message
     */
    public static function setWarning(string $message)
    {
        self::setMessageValue('warning', $message);
    }

    /**
     * @param $message
     */
    public static function setMessage($message)
    {
        self::setMessageValue('success', $message);
    }

    /**
     * @return string
     */
    public static function renderMessages()
    {
        if (!isset($_SESSION)) {
            return '';
        }

        if (!array_key_exists('messages', $_SESSION)) {
            return '';
        }

        $messages = '';
        foreach ($_SESSION['messages'] as $key => $message) {
            $messages .= '<p class="alert alert-' . $key . ' flash-' . $key . '">' . $message . "</p>";
            unset($_SESSION['messages'][$key]);
        }

        return $messages;
    }
}
