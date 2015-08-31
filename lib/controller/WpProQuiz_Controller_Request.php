<?php

class WpProQuiz_Controller_Request
{
    static protected $_post = null;
    static protected $_cookie = null;

    public static function getPost()
    {
        if (self::$_post == null) {
            self::$_post = self::clear($_POST);
        }

        return self::$_post;
    }

    public static function getPostValue($name)
    {
        if (self::$_post == null) {
            self::$_post = self::clear($_POST);
        }

        return isset(self::$_post[$name]) ? self::$_post[$name] : null;
    }

    public static function getCookie()
    {
        if (self::$_post == null) {
            self::$_cookie = self::clear($_COOKIE);
        }

        return self::$_cookie;
    }

    private static function clear($data)
    {
        if ($data !== null) {
            return stripslashes_deep($data);
        }

        return array();
    }
}