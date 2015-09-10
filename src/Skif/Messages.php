<?php

namespace Skif;


class Messages {

    protected static function setMessageValue($key, $value)
    {
        $_SESSION['messages'][$key] = $value;
    }

    /**
     * @param $message
     */
    public static function setError($message)
    {
        self::setMessageValue('danger',  $message);
    }

    /**
     * @param $message
     */
    public static function setMessage($message)
    {
        self::setMessageValue('success',  $message);
    }

    public static function renderMessages()
    {
        if (!isset($_SESSION)) {
            return '';
        }

        if (!array_key_exists('messages', $_SESSION)) {
            return '';
        }

        $messages = '';
        foreach($_SESSION['messages'] as $key => $message) {
            $messages .= '<p class="alert alert-' . $key . ' flash-' . $key . '">' . $message . "</p>";
            unset($_SESSION['messages'][$key]);
        }

        return $messages;
    }

}
